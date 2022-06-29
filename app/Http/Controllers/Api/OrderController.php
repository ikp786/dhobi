<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\OrderRescheduleRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\DriverOrderDetail;
use App\Http\Resources\DriverOrderList;
use App\Http\Resources\DriverOrderListResource;
use App\Http\Resources\UserOrderDetail;
use App\Http\Resources\UserOrderList;
use App\Models\AddOnService;
use App\Models\AddOnServiceMappingInCart;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CouponCartMapping;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Ratting;
use App\Models\Setting;
use App\Models\Support;
use App\Models\TimeSlot;
use App\Models\ZipCode;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Validator;
use Razorpay\Api\Api;

class OrderController extends BaseController
{
    function savePaymentResponse(Request $request)
    {
        try {
            $error_message = [
                'order_id.required'           => 'Order Id should be required',
            ];
            $rules = [
                'order_id'                   => 'required|exists:orders,id',
                'payment_status'             => 'required|In:Success,Failed',
            ];

            $validator = Validator::make($request->all(), $rules, $error_message);
            if ($validator->fails()) {
                return $this->sendFailed($validator->errors()->first(), 200);
            }
            if ($request->payment_status == 'Success') {
                \DB::beginTransaction();
                $orders  = auth()->user()->orders()->find($request->order_id);
                if (!isset($orders) || $orders == null) {
                    return $this->sendFailed('SORRY! WRONG ORDER ID', 200);
                }
                $orders->payment_status = $request->payment_status;
                $orders->is_order = 1;
                $orders->save();
                auth()->user()->carts()->delete();
                CouponCartMapping::where('user_id', auth()->user()->id)->delete();
                \DB::commit();
                return $this->sendSuccess('ORDER SAVE SUCCESSFULL');
            } else {
                \DB::beginTransaction();
                $orders  = auth()->user()->orders()->find($request->order_id);
                if (!isset($orders) || $orders == null) {
                    return $this->sendFailed('SORRY! WRONG ORDER ID', 200);
                }
                $orders->payment_status = $request->payment_status;
                $orders->save();
                \DB::commit();
                return $this->sendFailed('YOUR ORDER PAYMENT IS FAILED. PLEASE TRY AGAIN', 200);
            }
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function cancelOrder(Request $request)
    {
        $orders = auth()->user()->orders()->where('is_order', 1)->where('id', $request->order_id)->with('addresses')->first();
        if (!isset($orders)) {
            return $this->sendFailed('ORDER NOT FOUND', 200);
        }
        if ($orders->order_delivery_status != 'Pending') {
            return $this->sendFailed('SORRY! YOU CANNOT CANCEL ORDER AFTER ' . $orders->order_delivery_status, 200);
        }
        $orders->order_delivery_status = 'Canceled';
        $orders->save();
        return $this->sendSuccess('ORDER CANCELED SUCCESSFULL');
    }

    public function createOrder(StoreOrderRequest $request)
    {
        try {
            if (auth()->user()->status == 'Inactive') {
				return $this->sendFailed('YOU ARE BLOCK BY ADMIN', 200);
			}
            $checkCarts = Cart::where('user_id', auth()->user()->id)->get()->toArray();
            if (empty($checkCarts)) {
                return $this->sendFailed('SORRY! NO PRODUCT FOUND IN CART', 200);
            }

            $address = Address::find($request->address_id);

            $checkExist = ZipCode::where('zipcode', $address->pincode)->first();
            if (!isset($checkExist) || empty($checkExist)) {
                return $this->sendFailed('Delivery Service are not available for this pincode please contact to support.', 200);
            }
            $delivery_charge                = $checkExist->delivery_charge;

            $total_amount = 0;
            foreach ($checkCarts as $key => $val) {
                $productsData           = Product::find($val['product_id']);
                $total_amount           = $total_amount + (int)$productsData->price * (int)$val['product_quantity'];
                // $single_product_price   = (int)$productsData->price;
            }
            $checkcoupons = CouponCartMapping::where('user_id', auth()->user()->id)->first();
            if (isset($checkcoupons->id) && $checkcoupons != '') {
                $coupon_code        = $checkcoupons->coupon_code;
                $coupon_amount      = $checkcoupons->coupon_amount;
            } else {
                $coupon_code        = '';
                $coupon_amount      = 0;
            }

            $total_amounts = $total_amount + $delivery_charge - $coupon_amount;

            // if ($request->payment_method == 'Online') {
            //     $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRATE'));
            //     $orderData = [
            //         'receipt'         => 'rcptid_11',
            //         'amount'          => $total_amount * 100, // 39900 rupees in paise
            //         'currency'        => 'INR'
            //     ];
            //     $razorpayOrder = $api->order->create($orderData);
            // }
            \DB::beginTransaction();

            $order_id = date('Ymd') . rand(1000, 9999) . auth()->user()->id;
            // SAVE ORDER ID IN ORDER TABLE
            $orders                         = new Order();
            $orders->order_number           = $order_id;
            $orders->coupon_code            = $coupon_code;
            $orders->coupon_amount          = $coupon_amount;
            // if ($request->payment_method == 'Online') {
            //     $orders->razorpay_id        = $razorpayOrder->id;
            // } else {
            //     $orders->is_order = 1;
            // }
            $p_time_slot_data                 = TimeSlot::find($request->pickup_time_slot_id);
            $d_time_slot_data                 = TimeSlot::find($request->delivery_time_slot_id);
            $address = Address::find($request->address_id);
            $orders->address_id             = $request->address_id;
            $orders->address                = $address->address;
            $orders->pincode                = $address->pincode;
            // $orders->pickup_date            = date('Y-m-d', strtotime($request->pickup_date));
            // $orders->delivery_date          = date('Y-m-d', strtotime($request->delivery_date));
            $orders->pickup_date            = DateTime::createFromFormat('d/m/Y', $request->pickup_date)->format('Y-m-d');
            $orders->delivery_date          = DateTime::createFromFormat('d/m/Y', $request->delivery_date)->format('Y-m-d');
            $orders->remark                 = $request->remark;
            $orders->pickup_time_slot_id    = $request->pickup_time_slot_id;
            $orders->delivery_time_slot_id  = $request->delivery_time_slot_id;
            $orders->pickup_time            =  date('h:i A', strtotime($p_time_slot_data->from)) . ' to ' .  date('h:i A', strtotime($p_time_slot_data->to));
            $orders->delivery_time          = date('h:i A', strtotime($d_time_slot_data->from)) . ' to ' . date('h:i A', strtotime($d_time_slot_data->to));
            $orders->order_amount           = $total_amounts;
            $orders->deliver_charge         = $delivery_charge;
            $orders->mobile                 = auth()->user()->mobile;
            $orders->email                  = auth()->user()->email;
            $orders->payment_method         = $request->payment_method;
            $orders->payment_status         = 'Pending';
            $orders = auth()->user()->orders()->save($orders);
            $carts = Cart::where('user_id', auth()->user()->id)->get();
            foreach ($carts as $key => $value) {
                // GET PRODCUT DETAIL PRODUCT TABLE
                $products                                   = Product::find($value->product_id);
                // ADD ON SERVICES 
                $add_on_service_ids    = AddOnServiceMappingInCart::where(['cart_id' => $value->id])->pluck('add_on_service_id')->join(',');

                $add_on_service_arr    = AddOnService::select('id', 'price', 'title')->whereIn('id', explode(',', $add_on_service_ids))->get()->toArray();
                $add_on_service_price  = array_sum(array_column($add_on_service_arr, 'price'));
                $add_on_service_encode = AddOnService::whereIn('id', explode(',', $add_on_service_ids))->pluck('price', 'title');

                // TOTAL AMOUNT SUM 
                $total_amount                               = $products->price * $value->product_quantity;
                // SAVE ORDER PRODUCT DETAIL
                $order_product                              = new OrderProduct();
                $order_product->order_id                    = $orders->id;
                $order_product->product_id                  = $products->id;
                $order_product->category_id                 = $products->category_id;
                $order_product->sub_category_id             = $products->sub_category_id;
                $order_product->product_name                = $products->name;
                $order_product->product_quantity            = $value->product_quantity;
                $order_product->total_amount                = $products->price * $value->product_quantity;
                $order_product->add_on_service_amount       = $add_on_service_price;
                $order_product->add_on_services             = $add_on_service_encode;
                $order_product->save();
                if ($request->payment_method == 'Cod') {
                    $value->delete();
                    $checkcoupons = CouponCartMapping::where('user_id', auth()->user()->id)->first();
                    if (!empty($checkcoupons)) {
                        $checkcoupons->delete();
                    }
                }
            }

            if ($request->payment_method == 'Online') {
                $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRATE'));
                $orderData = [
                    'receipt'         => 'rcptid_11',
                    'amount'          => $total_amount * 100, // 39900 rupees in paise
                    'currency'        => 'INR'
                ];
                $razorpayOrder = $api->order->create($orderData);
            }
            if ($request->payment_method == 'Online') {
                $orders->razorpay_id        = $razorpayOrder->id;
            } else {
                $orders->is_order = 1;
            }

            $orders->total_product_amount   = OrderProduct::where('order_id', $orders->id)->sum('total_amount');
            $orders->add_on_service_amount = OrderProduct::where('order_id', $orders->id)->sum('add_on_service_amount');
            $orders->order_amount           = $total_amounts + $orders->add_on_service_amount;
            $orders = auth()->user()->orders()->save($orders);
            // SAVE ORDER PLACE ADDRESS DETAILS
            // $address                        = new Address();
            // $address->order_id              = $orders->id;
            // $address->name                  = $request->name;
            // $address->mobile                = $request->mobile;
            // $address->email                 = $request->email;
            // $address->address               = $request->address;
            // $address->pincode               = $request->pincode;
            // $address->type                  = $request->address_type;
            // auth()->user()->address()->save($address);
            \DB::commit();
            if ($request->payment_method == 'Cod') {
                return $this->sendSuccess('ORDER CREATE SUCCESSFULL', ['razorpay_id' => "", 'order_id' => $orders->id]);
            }
            return $this->sendSuccess('ORDER CREATE SUCCESSFULL', ['razorpay_id' => $razorpayOrder->id, 'order_id' => $orders->id]);
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }



    public function orderReschedule(OrderRescheduleRequest $request)
    {
        // echo 'dfdfdfd';die;
        try {

            $orders = auth()->user()->orders()->where('is_order', 1)->where('order_delivery_status', 'Pending')->where('id', $request->order_id)->first();

            if (!isset($orders->id)) {
                return $this->sendFailed('UNAUTHORRIZED ACCESS', 200);
            }

            $p_time_slot_data               = TimeSlot::find($request->pickup_time_slot_id);
            $d_time_slot_data               = TimeSlot::find($request->delivery_time_slot_id);
            $orders->pickup_time_slot_id    = $request->pickup_time_slot_id;
            $orders->pickup_date            = DateTime::createFromFormat('d/m/Y', $request->pickup_date)->format('Y-m-d');
            $orders->delivery_date          = DateTime::createFromFormat('d/m/Y', $request->delivery_date)->format('Y-m-d');
            $orders->delivery_time_slot_id  = $request->delivery_time_slot_id;
            $orders->pickup_time            = date('h:i A', strtotime($p_time_slot_data->from)) . ' to ' .  date('h:i A', strtotime($p_time_slot_data->to));
            $orders->delivery_time          = date('h:i A', strtotime($d_time_slot_data->from)) . ' to ' . date('h:i A', strtotime($d_time_slot_data->to));
            $orders = auth()->user()->orders()->save($orders);
            return $this->sendSuccess('ORDER RESHEDULE  SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getUserOrderList($status)
    {
        try {
            if ($status == 'Pending' || $status == 'Deliver' || $status == 'Canceled') {
            } else {
                return $this->sendFailed('Sorry! Status accept only Pending or Canceled or Deliver', 400);
            }
            if ($status == 'Pending') {
                $orders = auth()->user()->orders()->where('is_order', 1)->where('order_delivery_status', $status)->orWhere('order_delivery_status', 'Pickup')->with('addresses')->orderBy('id', 'desc')->get();
            } else {
                $orders = auth()->user()->orders()->where('is_order', 1)->where('order_delivery_status', $status)->with('addresses')->orderBy('id', 'desc')->get();
            }
            // dd($orders);
            if (!isset($orders) || count($orders) == 0) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }

            return $this->sendSuccess('ORDER GET SUCCESSFULLY', UserOrderList::collection($orders));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getUserOrderDetail($order_id)
    {
        try {
            $orders = auth()->user()->orders()->where('is_order', 1)->where('id', $order_id)->with('addresses')->first();

            if (!isset($orders)) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', new UserOrderDetail($orders));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function driverDashboard()
    {
        try {
            $datadd = 'dfdfd';
            $orders = auth()->user()->driverOrders()->where('is_order', 1)->where('driver_id', auth()->user()->id)
                ->where(function ($query) use ($datadd) {
                    $query->where('order_delivery_status', 'Pending')->orWhere('order_delivery_status', 'Pickup');
                })->where('pickup_date', Carbon::today())
                ->with('addresses')->orderBy('id', 'desc')->get();
            $total_order = auth()->user()->driverOrders()->where('is_order', 1)->where('driver_id', auth()->user()->id)->count();
            $total_amount = auth()->user()->driverOrders()->where('is_order', 1)->where('order_delivery_status', 'Deliver')->where('driver_id', auth()->user()->id)->sum('deliver_charge');

            $profile_pic   = !empty(auth()->user()->profile_pic) ? asset('storage/app/public/user_images/' . auth()->user()->profile_pic) : asset('storage/user_images/logo.png');
            $profile      = [
                'name' => auth()->user()->name,
                'online_status' => auth()->user()->online_status,
                'profile_pic' => $profile_pic
            ];
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', ['order_list' => DriverOrderList::collection($orders), 'total_income' => $total_amount, 'total_order' => $total_order, 'driver_info' => $profile]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getDriverOrderList($status)
    {
        try {
            if ($status == 'Today' || $status == 'Upcoming' || $status == 'Deliver') {
            } else {
                return $this->sendFailed('Sorry! Status Today only Upcoming or  Deliver', 200);
            }
            if ($status == 'Today') {
                $orders = auth()->user()->driverOrders()->where('is_order', 1)->where('order_delivery_status', '!=', 'Deliver')->whereDate('pickup_date', Carbon::today())->with('addresses')->orderBy('id', 'desc')->get();
            } elseif ($status == 'Upcoming') {
                $orders = auth()->user()->driverOrders()->where('is_order', 1)->where('order_delivery_status', '!=', 'Deliver')->whereDate('pickup_date', '>', Carbon::today())->with('addresses')->orderBy('id', 'desc')->get();
            } else {
                $orders = auth()->user()->driverOrders()->where('is_order', 1)->where('order_delivery_status', 'Deliver')
                    ->with('addresses')->orderBy('id', 'desc')->get();
            }
            if (!isset($orders[0]->id)) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', DriverOrderListResource::collection($orders));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getDriverOrderDetail($order_id)
    {
        try {
            $orders = auth()->user()->driverOrders()->where('is_order', 1)->where('id', $order_id)->with('addresses')->first();
            if (!isset($orders)) {
                return $this->sendFailed('ORDER NOT FOUND', 200);
            }
            return $this->sendSuccess('ORDER GET SUCCESSFULLY', new DriverOrderDetail($orders));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // SENT FEEDBACK
    public function createFeedback(Request $request)
    {
        $error_message =     [
            'order_id.required'            => 'Order ID should be required',
            'ratting_star.required'        => 'Ratting should be required',
            'ratting_comment.required'     => 'Feedback comment should be required',
            'order_id.unique'              => 'You have already submited Feedback this order'
        ];
        $rules = [
            'order_id'                  => 'required|exists:orders,id|unique:rattings,order_id',
            'ratting_star'              => 'required',
            'ratting_comment'           => 'required',
        ];
        $validator = Validator::make($request->all(), $rules, $error_message);
        if ($validator->fails()) {
            return $this->sendFailed($validator->errors()->first(), 200);
        }
        try {
            \DB::beginTransaction();
            $ratting = new Ratting();
            $ratting->fill($request->all());
            $ratting = auth()->user()->ratting()->save($ratting);
            \DB::commit();
            return $this->sendSuccess('FEEDBACK SENT SUCCESSFULLY');
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function orderDeliverByDriver(Request $request)
    {
        $error_message =     [
            'order_id.required'            => 'Order ID should be required',
            'order_id.exists'              => 'wrong order id',
            'driver_payment_type.required' => 'Driver Payment type required if payment mehotd Cod'
        ];
        $rules = [
            'order_id'                  => 'required|exists:orders,id',
        ];
        $orders = Order::where(['id' => $request->order_id, 'driver_id' => auth()->user()->id])->where('is_order', 1)->first();
        if (!isset($orders)) {
            return $this->sendFailed('UNAUTHORRIZED ACCESS', 200);
        }
        if ($orders->payment_method == 'Cod') {
            $rules['driver_payment_type']  = 'required';
        }
        $validator = Validator::make($request->all(), $rules, $error_message);
        if ($validator->fails()) {
            return $this->sendFailed($validator->errors()->first(), 200);
        }
        $orders = Order::find($request->order_id);
        if ($orders->payment_method == 'Cod') {
            $orders->driver_payment_type = $request->driver_payment_type;
        }
        $orders->order_delivery_status  = 'Deliver';
        $orders->payment_status      = 'Success';
        $orders->save();
        return $this->sendSuccess('ORDER DELIVER SUCCESSFULLY');
    }

    function orderPickupByDriver(Request $request)
    {
        $error_message =     [
            'order_id.required'            => 'Order ID should be required',
            'order_id.exists'              => 'wrong order id',
        ];
        $rules = [
            'order_id'                     => 'required|exists:orders,id',
        ];
        $orders = Order::where(['id' => $request->order_id, 'driver_id' => auth()->user()->id])->where('is_order', 1)->first();
        if (!isset($orders)) {
            return $this->sendFailed('UNAUTHORRIZED ACCESS', 200);
        }
        $validator = Validator::make($request->all(), $rules, $error_message);
        if ($validator->fails()) {
            return $this->sendFailed($validator->errors()->first(), 200);
        }
        $orders = Order::find($request->order_id);
        $orders->order_delivery_status  = 'Pickup';
        $orders->save();
        return $this->sendSuccess('ORDER PICKUP SUCCESSFULLY');
    }

    function userSupport(Request $request)
    {
        try {
            $error_message =     [
                'order_id.required'            => 'Order ID should be required',
                'order_id.exists'              => 'wrong order id',
            ];
            $rules = [
                'order_id'                  => 'required|exists:orders,order_number',
            ];

            $validator = Validator::make($request->all(), $rules, $error_message);
            if ($validator->fails()) {
                // return $this->sendFailed($validator->errors()->first(), 200);
            }
            \DB::beginTransaction();
            $support = new Support();
            $support->user_id   = auth()->user()->id;
            $support->order_id  = $request->order_id;
            $support->reason    = $request->reason;
            $support->save();
            \DB::commit();

            return $this->sendSuccess('YOUR REQUEST SUBMITED SUCCESSFULLY');
        } catch (\Throwable $e) {
            \DB::rollback();
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
}
