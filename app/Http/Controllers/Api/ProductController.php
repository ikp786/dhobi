<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CartListCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\OfferResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TimeSlotResource;
use App\Models\AddOnService;
use App\Models\AddOnServiceMappingInCart;
use App\Models\AddOnServiceMappingInProduct;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponCartMapping;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\SubCategory;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\ZipCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function userDashboard(Request $request)
    {
        try {
            $userData = User::find($request->user_id);
            $user_id = isset($userData->id) ? $userData->id : '';
            $cagetory_list = Category::OrderBy('name', 'asc')->where('status', 1)->limit(10)->get(['id', 'name', 'image']);
            $profile_pic   = !empty($userData->profile_pic) ? asset('storage/app/public/user_images/' . @auth()->user()->profile_pic) : asset('storage/user_images/logo.png');
            $get_address   = Address::where('user_id', $request->user_id)->where('is_favorite', 1)->first();
            $profile       = [
                'name'           => isset($userData->name) ? $userData->name : '',
                'address'        => isset($get_address->address) ? $get_address->address : '',
                'address_type'   => isset($get_address->type) ? $get_address->type : '',
                'profile_pic'    => $profile_pic
            ];
            $sliders = Slider::get();
            return $this->sendSuccess('DASHBOARD GET SUCCESSFULLY', ['category' => CategoryResource::collection($cagetory_list), 'user_data' => $profile, 'banner' => BannerResource::collection($sliders)]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // PRODUCT CATEGORY LIST
    public function getCategoryList()
    {
        try {
            $cagetory_list = Category::OrderBy('name', 'asc')->where('status', 1)->get(['id', 'name', 'image']);
            if (!isset($cagetory_list) || count($cagetory_list) == 0) {
                return $this->sendFailed('CATEGORY NOT FOUND', 200);
            }
            return $this->sendSuccess('CATEGORY GET SUCCESSFULLY', CategoryResource::collection($cagetory_list));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // PRODUCT SUB CATEGORY LIST
    public function getSubCategoryByCategory($id)
    {
        try {
            $cagetory_list = SubCategory::OrderBy('name', 'asc')->where(['status' => 1, 'category_id' => $id])->get(['id', 'name', 'category_id']);
            if (!isset($cagetory_list) || count($cagetory_list) == 0) {
                return $this->sendFailed('SUB CATEGORY NOT FOUND', 200);
            }
            return $this->sendSuccess('SUB CATEGORY GET SUCCESSFULLY', ($cagetory_list));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getProductCategoryWise(Request $request)
    {
        try {
            $error_message = [
                'category_id.required' => 'Category Id should be required',
            ];
            $rules = [
                'category_id'          => 'required|exists:categories,id',
                'sub_category_id'      => 'required|exists:sub_categories,id',
            ];
            $validator = Validator::make($request->all(), $rules, $error_message);
            if ($validator->fails()) {
                return $this->sendFailed($validator->errors()->first(), 200);
            }
            $products  = Product::where(['category_id' => $request->category_id, 'sub_category_id' => $request->sub_category_id])->with('categories', 'addOnServiceMappingInProduct.addOnService')->get();
            if (!isset($products) || count($products) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PRODUCT GET SUCCESSFULLY', ProductResource::collection($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getAddOnServiceByProductId($product_id)
    {
        try {
            $products  = Product::find($product_id);
            if (!isset($products->id)) {
                return $this->sendFailed('PRODUCT ID NOT FOUND', 200);
            }
            $add_on_service_id = AddOnServiceMappingInProduct::where('product_id', $product_id)->pluck('add_on_service_id')->join(',');
            // dd($add_on_service_id);
            if (!isset($add_on_service_id)) {
                return $this->sendFailed('NO ADD ON SERVICE IN THIS PRODUCT', 200);
            }
            $service_id = explode(',', $add_on_service_id);
            // dd($service_id);
            $add_on_service = AddOnService::whereIn('id', $service_id)->get(['id', 'title', 'price']);
            // dd($add_on_service);

            return $this->sendSuccess('PRODUCT GET SUCCESSFULLY', $add_on_service);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getSearchProduct(Request $request)
    {
        try {
            if ($request->product_name == '') {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            $products  = Product::with('categories', 'addOnServiceMappingInProduct.addOnService')->where('name', 'LIKE', "%" . $request->product_name . "%")->get();
            if (!isset($products) || count($products) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PRODUCT GET SUCCESSFULLY', ProductResource::collection($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getProductDetails($id)
    {
        try {
            $products  = Product::with('categories', 'addOnServiceMappingInProduct.addOnService')->where('id', $id)->first();
            if (!isset($products) || empty($products)) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            return $this->sendSuccess('PRODUCT DETAIL GET SUCCESSFULLY', new ProductResource($products));
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function deleteCoupon(Request $request)
    {
        try {
            $checkExist2 = CouponCartMapping::where(['user_id' => auth()->user()->id])->first();
            if (empty($checkExist2)) {
                return $this->sendFailed('COUPON NOT FOUND', 200);
            }
            $checkExist2->delete();
            return $this->sendSuccess('COUPON DELETE SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function applyCoupon(Request $request)
    {
        try {
            // echo auth()->user();die;
            $validator = Validator::make(
                $request->all(),
                [
                    'coupon_code'             => 'required|exists:coupons,coupon_code',
                    'device_token'            => 'required'
                ],
                [
                    'coupon_code.required'    => 'Coupon Code should be required'
                ]
            );

            if ($validator->fails()) {
                return $this->sendFailed($validator->errors()->first(), 200);
            }

            $carts  = Cart::where('user_id', auth()->user()->id)->first();
            // echo $carts;die;
            if (!isset($carts) || empty($carts)) {
                return $this->sendFailed('PRODUCT NOT FOUND IN CART', 200);
            }

            $checkExist = Coupon::where('coupon_code', $request->coupon_code)->first();
            // dd($checkExist);
            if ($checkExist->start_date > date('Y-m-d')) {
                return $this->sendFailed('THIS COUPON CODE IS INVALID', 200);
            }

            if (!empty($checkExist->end_date < date('Y-m-d'))) {
                return $this->sendFailed('THIS COUPON CODE IS EXPIRED', 200);
            }

            $checkExist2 = CouponCartMapping::where(['user_id' => auth()->user()->id])->first();
            if (!empty($checkExist2)) {
                return $this->sendFailed('COUPON ALREADY USE IN CART', 200);
            }

            $checkExist3 = Order::where(['coupon_code' => $request->coupon_code, 'user_id' => auth()->user()->id])->first();
            if (!empty($checkExist3)) {
                return $this->sendFailed('THIS COUPON ALREADY USED', 200);
            }

            $total_add_on_service_ids   = AddOnServiceMappingInCart::where(['user_id' => auth()->user()->id])->get('add_on_service_id');

            $total_add_on_service_price = 0;
            foreach ($total_add_on_service_ids as $key => $value2) {
                $serice = AddOnService::find($value2->add_on_service_id);
                $total_add_on_service_price = $total_add_on_service_price + $serice->price;
            }

            $total_sum = 0;
            $get_cart_data1  = Cart::where(['user_id' => auth()->user()->id])->get();
            foreach ($get_cart_data1 as $key => $value1) {
                $products = Product::find($value1->product_id);
                $total_sum = $total_sum + $products->price * $value1->product_quantity;
            }
            $total_cart_amt = (int)$total_add_on_service_price + (int)$total_sum;

            if (!empty($total_cart_amt < $checkExist->coupon_amount)) {
                return $this->sendFailed('YOU CANNOT APPLICABLE FOR THIS COUPON', 200);
            }

            $save  = new CouponCartMapping();
            $save->fill($request->all());
            $save->coupon_code   = $checkExist->coupon_code;
            $save->coupon_amount = $checkExist->coupon_amount;
            $save->cart_id       = $carts->id;
            $save->user_id       = auth()->user()->id;
            $save->device_token  = $request->device_token;
            $save->coupon_code   = $checkExist->coupon_code;
            $save->save();

            return $this->sendSuccess('COUPON APPLY SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function addToCart(AddToCartRequest $request)
    {
        try {
            $products  = Product::find($request->product_id);
            if (!isset($products) || empty($products)) {
                return $this->sendFailed('PRODUCT ID NOT FOUND', 200);
            }
            $couponExist = CouponCartMapping::where('device_token', $request->device_token)->first();
            if (isset($couponExist->id)) {
                $couponExist->delete();
            }
            // CHEK THIS PRODUCT ALREADY ADDED OR NOT IN CART
            $checkExist = Cart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token])->first();
            // UPDATE PRODUCT IN CART
            if (!empty($checkExist)) {
                // ADD PRODUCT IN CART            
                $checkExist->delete();
                $add_to_cart = new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                // $carts = auth()->user()->carts()->save($add_to_cart);
                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {
                            $addService                    = new AddOnServiceMappingInCart();
                            $addService->cart_id           = $add_to_cart->id;
                            $addService->user_id           = $request->user_id;
                            $addService->device_token      = $request->device_token;
                            $addService->product_id        = $products->id;
                            $addService->add_on_service_id = $val;
                            $addService->save();
                        }
                    }
                }
            } else {
                // ADD PRODUCT IN CART             
                $add_to_cart = new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {
                            $addService                    = new AddOnServiceMappingInCart();
                            $addService->cart_id           = $add_to_cart->id;
                            $addService->user_id           = $request->user_id;
                            $addService->device_token      = $request->device_token;
                            $addService->product_id        = $products->id;
                            $addService->add_on_service_id = $val;
                            $addService->save();
                        }
                    }
                }
            }

            $get_cart_data1  = Cart::where(['device_token' => $request->device_token])->get();
            $product_count   = count($get_cart_data1);

            return $this->sendSuccess('PRODCUT ADDED IN CARD SUCCESSFULLY', ['product_count' => $product_count]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    // PRODUCT DELETE IN CART
    public function deleteProdcutInCart(Request $request)
    {
        try {
            $checkExist = Cart::where(['id' => $request->cart_id, 'device_token' => $request->device_token])->first();
            if (!$checkExist) {
                return $this->sendFailed('PRODUCT NOT FOUND', 200);
            }
            $couponExist = CouponCartMapping::where('device_token', $request->device_token)->first();
            if (isset($couponExist->id)) {
                $couponExist->delete();
            }
            $checkExist->delete();
            return $this->sendSuccess('PRODUCT DELETE IN CART SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }


    // ADD ON SERVICE DELETE IN CART
    public function deleteAddOnServiceInCart(Request $request)
    {
        try {
            $error_message = [
                'cart_id.required'     => 'Cart Id should be required',
            ];
            $rules = [
                'cart_id'             => 'required|exists:carts,id',
                'device_token'        => 'required',
                'product_id'          => 'required|exists:add_on_service_mapping_in_carts,product_id',
                'add_on_service_id'   => 'required|exists:add_on_service_mapping_in_carts,add_on_service_id',
            ];
            $validator = Validator::make($request->all(), $rules, $error_message);
            if ($validator->fails()) {
                return $this->sendFailed($validator->errors()->first(), 200);
            }
            $checkExist = AddOnServiceMappingInCart::where(['cart_id' => $request->cart_id, 'product_id' => $request->product_id, 'add_on_service_id' => $request->add_on_service_id, 'device_token' => $request->device_token])->first();
            if (!$checkExist) {
                return $this->sendFailed('ADD ON SERVICE NOT FOUND', 200);
            }

            $couponExist = CouponCartMapping::where('device_token', $request->device_token)->first();
            if (isset($couponExist->id)) {
                $couponExist->delete();
            }

            $checkExist->delete();
            return $this->sendSuccess('ADD ON SERVICE DELETE IN CART SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }


    public function getCartDetail(Request $request)
    {
        // dd($request->all());
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'device_token'             => 'required',
                    // 'user_id'                  => 'exists:users,id'
                ],
                [
                    'device_token.required'    => 'Device Token should be required'
                ]
            );
            // echo 'dfd';die;
            if ($validator->fails()) {
                return $this->sendFailed($validator->errors()->first(), 200);
            }

            $get_cart_data  = Cart::where(['device_token' => $request->device_token])->get();
            if (!isset($get_cart_data) || count($get_cart_data) == 0) {
                return $this->sendFailed('PRODUCT NOT FOUND IN CART', 200);
            }

            if (isset($request->user_id) && $request->user_id != '') {
                Cart::where('device_token', $request->device_token)->update(['user_id' => $request->user_id]);
                AddOnServiceMappingInCart::where('device_token', $request->device_token)->update(['user_id' => $request->user_id]);
            }

            $categories =  Cart::where('device_token', $request->device_token)->groupBy('category_id')->pluck('category_id');
            $product_data = [];
            $product_array = [];
            $category_name = '';
            $total_price_product = 0;
            foreach ($categories as $key =>  $category) {
                $category_name =  Category::where('id', $category)->value('name');
                $cart_data  =  Cart::where('device_token', $request->device_token)->where('category_id', $category)->get();
                foreach ($cart_data as $key1 => $cart) {
                    $p_data = Product::find($cart->product_id);
                    $add_on_service_ids  = AddOnServiceMappingInCart::where(['cart_id' => $cart->id])->pluck('add_on_service_id')->join(',');

                    $add_on_service_arr  = AddOnService::select('id', 'price', 'title')->whereIn('id', explode(',', $add_on_service_ids))->get()->toArray();
                    $add_on_service_price = array_sum(array_column($add_on_service_arr, 'price'));
                    $p_image        =  asset('storage/app/public/product_images/' . $p_data->image);
                    $product_data[$key1] = ['cart_id' => $cart->id, 'product_id' => $p_data->id, 'product_name' => $p_data->name, 'product_image' => $p_image, 'per_product_price' => $p_data->price, 'product_total_per' => $p_data->price * $cart->product_quantity, 'product_quantity' => $cart->product_quantity, 'category_id' => $category, 'add_on_services' => $add_on_service_arr, 'add_on_service_price' => $add_on_service_price];
                    $total_price_product = $total_price_product + $p_data->price * $cart->product_quantity;
                }
                $product_array[$key]['category_name'] = $category_name;
                $product_array[$key]['product'] = $product_data;
            }
            $total_add_on_service_ids   = AddOnServiceMappingInCart::where(['device_token' => $request->device_token])->get('add_on_service_id');

            $total_add_on_service_price = 0;
            foreach ($total_add_on_service_ids as $key => $value2) {
                $serice = AddOnService::find($value2->add_on_service_id);
                $total_add_on_service_price = $total_add_on_service_price + $serice->price;
            }

            $total_sum = 0;
            $get_cart_data1  = Cart::where(['device_token' => $request->device_token])->get();
            $product_count   = count($get_cart_data1);
            foreach ($get_cart_data1 as $key => $value1) {
                $products = Product::find($value1->product_id);
                $total_sum = $total_sum + $products->price * $value1->product_quantity;
            }

            $get_address   = Address::where('user_id', $request->user_id)->where('is_favorite', 1)->first();
            $address       = [
                'address_id'     => isset($get_address->id) ? $get_address->id : '',
                'address'        => isset($get_address->address) ? $get_address->address : '',
                'address_type'   => isset($get_address->type) ? $get_address->type : '',
                'pincode'        => isset($get_address->pincode) ? $get_address->pincode : '',
                'location'       => isset($get_address->location) ? $get_address->location : '',
                'latitude'       => isset($get_address->latitude) ? $get_address->latitude : '',
                'longitude'      => isset($get_address->longitude) ? $get_address->longitude : '',
                'name'           => isset($get_address->name) ? $get_address->name : '',
                'mobile'         => isset($get_address->mobile) ? $get_address->mobile : '',
                'email'          => isset($get_address->email) ? $get_address->email : '',
            ];

            // $delivery_charge = Setting::first();
            $checkExist  = ZipCode::where('zipcode', @$get_address->pincode)->first();
            $discount    = 0;
            $discount    = CouponCartMapping::where(['user_id' => $request->user_id])->value('coupon_amount');

            if (!isset($discount) || $discount == '') {
                $discount = 0;
            }

            $coupon_code = CouponCartMapping::where(['user_id' => $request->user_id])->value('coupon_code');

            if (!isset($coupon_code) || $coupon_code == '') {
                $coupon_code = '';
            }

            $delivery_charge                = isset($checkExist->delivery_charge) ? $checkExist->delivery_charge : 0;
            $total_amount = $delivery_charge + $total_sum + $total_add_on_service_price - $discount;
            $data = ['cart_data' => $product_array, 'product_count' => $product_count, 'add_on_service_charge' => $total_add_on_service_price, 'delivery_charge' => $delivery_charge, 'sub_total' => $total_price_product, 'coupon_discount' => $discount, 'coupon_code' => $coupon_code, 'total_order_sum' => $total_amount, 'address' => $address];

            return $this->sendSuccess('CART DATA GET SUCCESSFULLY', $data);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function getTimeSlot(Request $request)
    {
        try {
            $cagetory_list = TimeSlot::select('from', 'to', 'id')->get();
            if (!isset($cagetory_list) || count($cagetory_list) == 0) {
                return $this->sendFailed('TIMESLOT NOT FOUND', 200);
            }

            $get_cart_data  = Cart::where('user_id', $request->user_id)->get();
            if (!isset($get_cart_data) || count($get_cart_data) == 0) {
                // return $this->sendFailed('PRODUCT NOT FOUND IN CART', 200);
            }

            if (isset($request->user_id) && $request->user_id != '') {
                Cart::where('user_id', $request->user_id)->update(['user_id' => $request->user_id]);
                AddOnServiceMappingInCart::where('user_id', $request->user_id)->update(['user_id' => $request->user_id]);
            }

            $categories =  Cart::where('user_id', $request->user_id)->groupBy('category_id')->pluck('category_id');
            $product_data = [];
            $product_array = [];
            $category_name = '';
            $total_price_product = 0;
            foreach ($categories as $key =>  $category) {
                $category_name =  Category::where('id', $category)->value('name');
                $cart_data  =  Cart::where('user_id', $request->user_id)->where('category_id', $category)->get();
                foreach ($cart_data as $key1 => $cart) {
                    $p_data = Product::find($cart->product_id);
                    $add_on_service_ids  = AddOnServiceMappingInCart::where(['cart_id' => $cart->id])->pluck('add_on_service_id')->join(',');

                    $add_on_service_arr  = AddOnService::select('id', 'price', 'title')->whereIn('id', explode(',', $add_on_service_ids))->get()->toArray();
                    $add_on_service_price = array_sum(array_column($add_on_service_arr, 'price'));
                    $p_image        =  asset('storage/app/public/product_images/' . $p_data->image);
                    $product_data[$key1] = ['cart_id' => $cart->id, 'product_id' => $p_data->id, 'product_name' => $p_data->name, 'product_image' => $p_image, 'per_product_price' => $p_data->price, 'product_total_per' => $p_data->price * $cart->product_quantity, 'product_quantity' => $cart->product_quantity, 'category_id' => $category, 'add_on_services' => $add_on_service_arr, 'add_on_service_price' => $add_on_service_price];
                    $total_price_product = $total_price_product + $p_data->price * $cart->product_quantity;
                }
                $product_array[$key]['category_name'] = $category_name;
                $product_array[$key]['product'] = $product_data;
            }
            $total_add_on_service_ids   = AddOnServiceMappingInCart::where(['user_id' => $request->user_id])->get('add_on_service_id');

            $total_add_on_service_price = 0;
            foreach ($total_add_on_service_ids as $key => $value2) {
                $serice = AddOnService::find($value2->add_on_service_id);
                $total_add_on_service_price = $total_add_on_service_price + $serice->price;
            }

            $total_sum = 0;
            $get_cart_data1  = Cart::where(['user_id' => $request->user_id])->get();
            $product_count   = count($get_cart_data1);
            foreach ($get_cart_data1 as $key => $value1) {
                $products = Product::find($value1->product_id);
                $total_sum = $total_sum + $products->price * $value1->product_quantity;
            }

            $get_address   = Address::where('user_id', $request->user_id)->where('is_favorite', 1)->first();
            $address       = [
                'address_id'     => isset($get_address->id) ? $get_address->id : '',
                'address'        => isset($get_address->address) ? $get_address->address : '',
                'address_type'   => isset($get_address->type) ? $get_address->type : '',
                'pincode'        => isset($get_address->pincode) ? $get_address->pincode : '',
                'location'       => isset($get_address->location) ? $get_address->location : '',
                'latitude'       => isset($get_address->latitude) ? $get_address->latitude : '',
                'longitude'      => isset($get_address->longitude) ? $get_address->longitude : '',
                'name'           => isset($get_address->name) ? $get_address->name : '',
                'mobile'         => isset($get_address->mobile) ? $get_address->mobile : '',
                'email'          => isset($get_address->email) ? $get_address->email : '',
            ];

            // $delivery_charge = Setting::first();
            $checkExist  = ZipCode::where('zipcode', @$get_address->pincode)->first();
            $discount    = 0;
            $discount    = CouponCartMapping::where(['user_id' => $request->user_id])->value('coupon_amount');

            if (!isset($discount) || $discount == '') {
                $discount = 0;
            }

            $coupon_code = CouponCartMapping::where(['user_id' => $request->user_id])->value('coupon_code');

            if (!isset($coupon_code) || $coupon_code == '') {
                $coupon_code = '';
            }

            $delivery_charge                = isset($checkExist->delivery_charge) ? $checkExist->delivery_charge : 0;
            $total_amount = $delivery_charge + $total_sum + $total_add_on_service_price - $discount;

            // $data = ['cart_data' => $product_array, 'product_count' => $product_count, 'add_on_service_charge' => $total_add_on_service_price, 'delivery_charge' => $delivery_charge, 'sub_total' => $total_price_product, 'coupon_discount' => $discount, 'coupon_code' => $coupon_code, 'total_order_sum' => $total_amount, 'address' => $address];

            return $this->sendSuccess('TIMESLOT GET SUCCESSFULLY', ['sloat' => TimeSlotResource::collection($cagetory_list), 'add_on_service_charge' => $total_add_on_service_price, 'delivery_charge' => $delivery_charge, 'sub_total' => $total_price_product, 'coupon_discount' => $discount, 'coupon_code' => $coupon_code, 'total_order_sum' => $total_amount, 'address' => $address]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
}
