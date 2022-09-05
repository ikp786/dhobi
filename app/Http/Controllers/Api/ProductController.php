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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    public function userDashboard(Request $request)
    {
        try {
            if (isset($request->user_id) && $request->user_id != '') {
                Auth::loginUsingId($request->user_id);
            }
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
            $cagetory_list = Category::OrderBy('id', 'asc')->where('status', 1)->get(['id', 'name', 'image']);
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
            $cagetory_list = SubCategory::OrderBy('id', 'asc')->where(['status' => 1, 'category_id' => $id])->get(['id', 'name', 'category_id']);
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
            if (!isset($add_on_service_id)) {
                return $this->sendFailed('NO ADD ON SERVICE IN THIS PRODUCT', 200);
            }
            $service_id = explode(',', $add_on_service_id);
            $add_on_service = AddOnService::whereIn('id', $service_id)->get(['id', 'title', 'price']);

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
    // PRODUCT CATEGORY LIST
    public function getCouponList()
    {
        try {
            $now = Carbon::now();
            $coupon_list = Coupon::whereDate('start_date', '<=', $now)
                ->whereDate('end_date', '>=', $now)
                ->where('status', 1)
                ->get(['coupon_code', 'minimum_order_amount', 'discount_percentage', 'max_discount_amount', 'start_date', 'end_date', 'description']);

            if (!isset($coupon_list) || count($coupon_list) == 0) {
                return $this->sendFailed('COUPON NOT FOUND', 200);
            }
            return $this->sendSuccess('COUPON GET SUCCESSFULLY', $coupon_list);
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
            if (!isset($carts) || empty($carts)) {
                return $this->sendFailed('PRODUCT NOT FOUND IN CART', 200);
            }

            $checkExist = Coupon::where('coupon_code', $request->coupon_code)->first();
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

            if (!empty($total_cart_amt < $checkExist->minimum_order_amount)) {
                return $this->sendFailed('YOU CANNOT APPLICABLE FOR THIS COUPON', 200);
            }

            $save  = new CouponCartMapping();
            $save->fill($request->all());
            $save->coupon_code          = $checkExist->coupon_code;
            $save->minimum_order_amount = $checkExist->minimum_order_amount;
            $save->discount_percentage  = $checkExist->discount_percentage;
            $save->max_discount_amount  = $checkExist->max_discount_amount;
            $save->cart_id              = $carts->id;
            $save->user_id              = auth()->user()->id;
            $save->device_token         = $request->device_token;
            $save->coupon_code          = $checkExist->coupon_code;
            $save->save();

            return $this->sendSuccess('COUPON APPLY SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
    function addToCart(AddToCartRequest $request)
    {
        try {


            $data = json_encode($request->all());
            \DB::table('tests')->insert(['log' => $data]);

            $products  = Product::find($request->product_id);
            if (!isset($products) || empty($products)) {
                return $this->sendFailed('PRODUCT ID NOT FOUND', 200);
            }
            $add_on_service = explode(",", $request->add_on_services);
            sort($add_on_service);

            $couponExist = CouponCartMapping::where('device_token', $request->device_token)->first();
            if (isset($couponExist->id)) {
                $couponExist->delete();
            }
            // CHEK THIS PRODUCT ALREADY ADDED OR NOT IN CART
            if (!empty($add_on_service)) {
                $checkExist = Cart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token, "add_on_services" => implode(",", $add_on_service)])->orderBy('created_at', 'desc')->first();
            } else {
                $checkExist = Cart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token, "add_on_services" => null])->orderBy('created_at', 'desc')->first();
            }




            // dd($AddOnServiceMappingInCart);
            if (isset($checkExist) && !empty($checkExist)) {
                // ADD PRODUCT IN CART

                $add_to_cart = $checkExist; //new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->sub_category_id     = $products->sub_category_id;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->add_on_services     = implode(",", $add_on_service);


                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {
                            $checkAddOnServiceExists = AddOnServiceMappingInCart::where(['cart_id' => $add_to_cart->id, 'product_id' => $request->product_id, 'add_on_service_id' => $val, 'device_token' => $request->device_token])->first();
                            if (isset($checkAddOnServiceExists->id)) {
                                $addService = $checkAddOnServiceExists;
                            } else {
                                $addService                = new AddOnServiceMappingInCart();
                            }
                            $addService->cart_id           = $add_to_cart->id;
                            $addService->user_id           = $request->user_id;
                            $addService->device_token      = $request->device_token;
                            $addService->product_id        = $products->id;
                            $addService->add_on_service_id = $val;
                            $addService->save();
                        }
                    }
                }
            } elseif (empty($checkExist)) {


                $add_to_cart = new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->sub_category_id     = $products->sub_category_id;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->add_on_services     = implode(",", $add_on_service);
                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {

                            $checkAddOnServiceExists = AddOnServiceMappingInCart::where(['cart_id' => $add_to_cart->id, 'product_id' => $request->product_id, 'add_on_service_id' => $val, 'device_token' => $request->device_token])->first();
                            if (isset($checkAddOnServiceExists->id)) {
                                $addService = $checkAddOnServiceExists;
                            } else {
                                $addService                = new AddOnServiceMappingInCart();
                            }
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
            $product_count   = Cart::where('device_token', $request->device_token)->sum('product_quantity');
            return $this->sendSuccess('PRODCUT ADDED IN CARD SUCCESSFULLY', ['product_count' => $product_count]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function updateCart(AddToCartRequest $request)
    {
        try {

            $products  = Product::find($request->product_id);
            if (!isset($products) || empty($products)) {
                return $this->sendFailed('PRODUCT ID NOT FOUND', 200);
            }
            $add_on_service = explode(",", $request->add_on_services);
            sort($add_on_service);

            $couponExist = CouponCartMapping::where('device_token', $request->device_token)->first();
            if (isset($couponExist->id)) {
                $couponExist->delete();
            }
            // CHEK THIS PRODUCT ALREADY ADDED OR NOT IN CART
            if (!empty($add_on_service)) {
                $checkExist = Cart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token, "add_on_services" => implode(",", $add_on_service)])->orderBy('created_at', 'desc')->first();
            } else {
                $checkExist = Cart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token, "add_on_services" => null])->orderBy('created_at', 'desc')->first();
            }




            // dd($AddOnServiceMappingInCart);
            if (isset($checkExist) && !empty($checkExist)) {
                // ADD PRODUCT IN CART
                $product_quantity = $checkExist->product_quantity + $request->product_quantity;
                $add_to_cart = $checkExist; //new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_quantity    = $product_quantity;
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->sub_category_id     = $products->sub_category_id;
                $add_to_cart->total_amount        = $products->price * $product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->add_on_services     = implode(",", $add_on_service);


                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {
                            $checkAddOnServiceExists = AddOnServiceMappingInCart::where(['cart_id' => $add_to_cart->id, 'product_id' => $request->product_id, 'add_on_service_id' => $val, 'device_token' => $request->device_token])->first();
                            if (isset($checkAddOnServiceExists->id)) {
                                $addService = $checkAddOnServiceExists;
                            } else {
                                $addService                = new AddOnServiceMappingInCart();
                            }
                            $addService->cart_id           = $add_to_cart->id;
                            $addService->user_id           = $request->user_id;
                            $addService->device_token      = $request->device_token;
                            $addService->product_id        = $products->id;
                            $addService->add_on_service_id = $val;
                            $addService->save();
                        }
                    }
                }
            } elseif (empty($checkExist)) {


                $add_to_cart = new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->sub_category_id     = $products->sub_category_id;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->add_on_services     = implode(",", $add_on_service);
                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {

                            $checkAddOnServiceExists = AddOnServiceMappingInCart::where(['cart_id' => $add_to_cart->id, 'product_id' => $request->product_id, 'add_on_service_id' => $val, 'device_token' => $request->device_token])->first();
                            if (isset($checkAddOnServiceExists->id)) {
                                $addService = $checkAddOnServiceExists;
                            } else {
                                $addService                = new AddOnServiceMappingInCart();
                            }
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
            $product_count   = Cart::where('device_token', $request->device_token)->sum('product_quantity');
            return $this->sendSuccess('PRODCUT ADDED IN CARD SUCCESSFULLY', ['product_count' => $product_count]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    function addToCartold(AddToCartRequest $request)
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

            $checkExist = Cart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token])->orderBy('created_at', 'desc')->first();

            // CHEK FOR WITHOUT ADD ON SERVICE
            if (isset($checkExist)) {
                $AddOnServiceMappingInCart = AddOnServiceMappingInCart::where('cart_id', $checkExist->id)->first();
            }

            // CHECK ADD ON SERVICE ALREADY EXISTS OR NOT
            $add_on_service = explode(",", $request->add_on_services);
            sort($add_on_service);
            $checkAddOnServiceExist = AddOnServiceMappingInCart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token])->whereIn('add_on_service_id', $add_on_service)->get();
            // dd($checkAddOnServiceExist);
            // UPDATE PRODUCT IN CART

            if (isset($checkAddOnServiceExist->cart_id)) {
                $checkExist = Cart::find($checkAddOnServiceExist->cart_id);
            }
            // dd($AddOnServiceMappingInCart);
            if (isset($checkAddOnServiceExist->cart_id) && !empty($checkExist)) {
                // ADD PRODUCT IN CART

                $add_to_cart = $checkExist; //new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->sub_category_id     = $products->sub_category_id;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->add_on_services        = implode(",", $add_on_service);


                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {
                            $checkAddOnServiceExists = AddOnServiceMappingInCart::where(['cart_id' => $add_to_cart->id, 'product_id' => $request->product_id, 'add_on_service_id' => $val, 'device_token' => $request->device_token])->first();
                            if (isset($checkAddOnServiceExists->id)) {
                                $addService = $checkAddOnServiceExists;
                            } else {
                                $addService                = new AddOnServiceMappingInCart();
                            }
                            $addService->cart_id           = $add_to_cart->id;
                            $addService->user_id           = $request->user_id;
                            $addService->device_token      = $request->device_token;
                            $addService->product_id        = $products->id;
                            $addService->add_on_service_id = $val;
                            $addService->save();
                        }
                    }
                }
            } elseif (!empty($checkExist) && !isset($AddOnServiceMappingInCart)  &&  !isset($request->add_on_services)) {

                // ADD PRODUCT IN CART
                // $checkExist->delete();
                $add_to_cart = $checkExist; //new Cart();
                $add_to_cart->fill($request->only('product_id', 'category_id', 'product_quantity', 'user_id', 'device_token'));
                $add_to_cart->product_amount      = $products->price;
                $add_to_cart->sub_category_id     = $products->sub_category_id;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->add_on_services        = implode(",", $add_on_service);
                $add_to_cart->save();
                if (isset($request->add_on_services)) {
                    $add_on_service = explode(",", $request->add_on_services);
                    foreach ($add_on_service as $key => $val) {
                        $chekSer = AddOnService::find($val);
                        if (isset($chekSer->id)) {

                            $checkAddOnServiceExists = AddOnServiceMappingInCart::where(['cart_id' => $add_to_cart->id, 'product_id' => $request->product_id, 'add_on_service_id' => $val, 'device_token' => $request->device_token])->first();
                            if (isset($checkAddOnServiceExists->id)) {
                                $addService = $checkAddOnServiceExists;
                            } else {
                                $addService                = new AddOnServiceMappingInCart();
                            }
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
                $add_to_cart->sub_category_id     = $products->sub_category_id;
                $add_to_cart->total_amount        = $products->price * $request->product_quantity;
                $add_to_cart->product_name        = $products->name;
                $add_to_cart->add_on_services        = implode(",", $add_on_service);
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
            $product_count   = Cart::where('device_token', $request->device_token)->sum('product_quantity');
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
            $couponExist = CouponCartMapping::where('device_token', $request->device_token)->first();
            if (isset($couponExist->id)) {
                $couponExist->delete();
            }

            $checkExist = AddOnServiceMappingInCart::where(['cart_id' => $request->cart_id, 'product_id' => $request->product_id, 'add_on_service_id' => $request->add_on_service_id, 'device_token' => $request->device_token])->first();
            if (!$checkExist) {
                return $this->sendFailed('ADD ON SERVICE NOT FOUND', 200);
            }
            $checkExist->delete();
            $total_prod__qty_in_cart = Cart::where('id', $request->cart_id)->value('product_quantity');
            $remainAddOnService = AddOnServiceMappingInCart::where(['cart_id' => $request->cart_id, 'product_id' => $request->product_id, 'device_token' => $request->device_token])->orderBy("add_on_service_id", "ASC")->pluck("add_on_service_id");
            // echo (implode(",",$remainAddOnService->toArray()));die;
            $checkCart = Cart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token, "add_on_services" => implode(",", $remainAddOnService->toArray())])->orderBy('created_at', 'desc')->first();
            Cart::where('id', $request->cart_id)->update([
                'add_on_services' => implode(",", $remainAddOnService->toArray())
            ]);

            if (!empty($checkCart)) {
                $newQTy = $checkCart->product_quantity + $total_prod__qty_in_cart;
                $checkCart->product_quantity = $newQTy;
                $checkCart->save();

                Cart::where('id', $request->cart_id)->delete();
            }


            return $this->sendSuccess('ADD ON SERVICE DELETE IN CART SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
    public function deleteAddOnServiceInCartOld(Request $request)
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

            $total_prod__qty_in_cart = Cart::where('id', $request->cart_id)->value('product_quantity');

            $cart_data = AddOnServiceMappingInCart::where(['product_id' => $request->product_id, 'device_token' => $request->device_token])->pluck('cart_id');

            $cart_id = Cart::whereNotIn('id', $cart_data)->where('device_token', $request->device_token)->value('id');
            $total_prod__qty_in_cart2 = Cart::where('id', $cart_id)->value('product_quantity');
            // echo $cart_id;die;
            if ($cart_id != '') {
                // $total_qty = (int)$total_prod__qty_in_cart + (int)$total_prod__qty_in_cart2;
                // echo $total_qty.'<br>'.$cart_id;die;
                // Cart::find($cart_id)->update(['product_quantity',$total_qty]);
                Cart::find($cart_id)->increment('product_quantity', $total_prod__qty_in_cart); //update(['product_quantity',$total_qty]);
                Cart::find($request->cart_id)->delete();
            } else {
                $checkExist->delete();
            }

            return $this->sendSuccess('ADD ON SERVICE DELETE IN CART SUCCESSFULLY');
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }

    public function disbursements()
    {
        $response =  Http::get(env('API_URL') . 'loan_application/getAllLoanApplications?status=Approved');
        $data = isset($response['data']) ?  $response['data'] : '';
        if ($response['success']) {
            $new_arr = array();
            foreach ($data as $key => $val) {
                $scheme_id = isset($val['scheme_id']) ? $val['scheme_id'] : '';
                $response1 =  Http::get(env('API_URL') . 'scheme/getSchemeDetails?scheme_id=' . $scheme_id);
                $data1 =  isset($response1['data'][0]) ?  $response1['data'][0] : '';
                $new_arr[$key] = $val;
                $new_arr[$key]['scheme_id'] = isset($data1['scheme_id']) ? $data1['scheme_id'] : '';
                $new_arr[$key]['scheme_name'] = isset($data1['scheme_name']) ? $data1['scheme_name'] : '';
            }
            return view('gold_loan.disbursements', ['getAllDisbursement' => $new_arr]);
        } else {
            return view('gold_loan.disbursements');
        }
    }

    public function getCartDetail(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'device_token'             => 'required',
                ],
                [
                    'device_token.required'    => 'Device Token should be required'
                ]
            );
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
            $product_array = [];
            $category_name = '';
            $total_price_product = 0;
            $total_add_on_service_prices = 0;
            foreach ($categories as $key =>  $category) {
                $sub_categories     = Cart::where(['category_id' => $category, 'device_token' => $request->device_token])->groupBy('sub_category_id')->pluck('sub_category_id');
                $category_name      = Category::where('id', $category)->value('name');

                $subb_cat_arr = [];
                foreach ($sub_categories as $sub_category_key => $sub_category) {
                    $product_data       = [];
                    $sub_category_name  = SubCategory::where('id', $sub_category)->value('name');

                    $cart_data     = Cart::where('device_token', $request->device_token)->where(['category_id' => $category, 'sub_category_id' => $sub_category])->get();
                    foreach ($cart_data as $key1 => $cart) {
                        $p_data = Product::find($cart->product_id);
                        $add_on_service_ids  = AddOnServiceMappingInCart::where(['cart_id' => $cart->id])->pluck('add_on_service_id')->join(',');
                        $add_on_service_arr  = AddOnService::select('id', 'price', 'title')->whereIn('id', explode(',', $add_on_service_ids))->get()->toArray();
                        $add_on_service_price = array_sum(array_column($add_on_service_arr, 'price')) * $cart->product_quantity;
                        $add_on_serviceAr2 = [];
                        foreach ($add_on_service_arr as $add_on_serviceAr) {

                            $add_on_serviceAr['total_add_on_service_price']  = $add_on_serviceAr['price'] * $cart->product_quantity;
                            $add_on_serviceAr2[] = $add_on_serviceAr;
                        }
                        $add_on_service_arr = $add_on_serviceAr2;

                        $total_add_on_service_prices = $total_add_on_service_prices + $add_on_service_price;
                        $p_image        =  asset('storage/app/public/product_images/' . $p_data->image);
                        $product_data[$key1] = ['cart_id' => $cart->id, 'product_id' => $p_data->id, 'product_name' => $p_data->name, 'product_image' => $p_image, 'per_product_price' => $p_data->price, 'product_total_per' => ( $p_data->price * $cart->product_quantity ) + $add_on_service_price, 'product_quantity' => $cart->product_quantity, 'category_id' => $category, 'sub_category_id' => $cart->sub_category_id, 'add_on_services' => $add_on_service_arr, 'add_on_service_price' => $add_on_service_price];
                        $total_price_product = $total_price_product + $p_data->price * $cart->product_quantity;
                    }
                    $subb_cat_arr[] = [
                        "sub_category_name" => $sub_category_name,
                        "product" => $product_data
                    ];
                    $product_array[$key]['category_name'] = $category_name;
                    $product_array[$key]['sub_category'] = $subb_cat_arr;
                }
            }

            // $total_add_on_service_ids   = AddOnServiceMappingInCart::where(['device_token' => $request->device_token])->get('add_on_service_id');
            // $total_add_on_service_price = 0;
            // foreach ($total_add_on_service_ids as $key => $value2) {
            //     $serice = AddOnService::find($value2->add_on_service_id);
            //     $total_add_on_service_price = $total_add_on_service_price + $serice->price;
            // }
            $total_sum = 0;
            $get_cart_data1  = Cart::where(['device_token' => $request->device_token])->get();
            $product_count   = Cart::where('device_token', $request->device_token)->sum('product_quantity');
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
            $checkExist          = ZipCode::where('zipcode', @$get_address->pincode)->first();
            $discount            = 0;
            $discount_percentage = CouponCartMapping::where(['user_id' => $request->user_id])->value('discount_percentage');
            $max_discount_amount = CouponCartMapping::where(['user_id' => $request->user_id])->value('max_discount_amount');

            $discount            = round(($total_sum + $total_add_on_service_prices) / 100 * $discount_percentage, 2);

            if ($discount >= $max_discount_amount) {
                $discount = $max_discount_amount;
            }

            if (!isset($discount) || $discount == '') {
                $discount = 0;
            }

            $coupon_code = CouponCartMapping::where(['device_token' => $request->device_token])->value('coupon_code');
            if (!isset($coupon_code) || $coupon_code == '') {
                $coupon_code = '';
            }
            $delivery_charge                = isset($checkExist->delivery_charge) ? $checkExist->delivery_charge : 0;
            $minimum_order_value            = isset($checkExist->minimum_order_value) ? $checkExist->minimum_order_value : 0;
            $total_product_and_service_amt  = $total_sum + $total_add_on_service_prices;
            if ($total_product_and_service_amt >= $minimum_order_value) {
                $delivery_charge = 0;
            }
            $total_amount = $delivery_charge + $total_sum + $total_add_on_service_prices - $discount;
            $discount_message  = '5% Discount';
            $data = ['cart_data' => $product_array, 'product_count' => $product_count, 'add_on_service_charge' => $total_add_on_service_prices, 'delivery_charge' => $delivery_charge, 'sub_total' => $total_price_product, 'coupon_discount' => $discount, 'coupon_code' => $coupon_code, 'total_order_sum' => $total_amount, 'discount_message' => $discount_message, 'address' => $address];
            return $this->sendSuccess('CART DATA GET SUCCESSFULLY', $data);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
    public function getTimeSlot(Request $request)
    {
        try {
            $cagetory_list = TimeSlot::where('status', 1)->select('from', 'to', 'id')->get();
            if (!isset($cagetory_list) || count($cagetory_list) == 0) {
                return $this->sendFailed('TIMESLOT NOT FOUND', 200);
            }

            $get_cart_data  = Cart::where('user_id', $request->user_id)->get();
            if (!isset($get_cart_data) || count($get_cart_data) == 0) {
                // return $this->sendFailed('PRODUCT NOT FOUND IN CART', 200);
            }

            if (isset($request->user_id) && $request->user_id != '') {
                Cart::where('user_id', $request->user_id)->update(['user_id' => $request->user_id]);
                AddOnServiceMappingInCart::where('user_id', $request->user_id)->where('device_token', $request->device_token)->update(['user_id' => $request->user_id,'device_token' => $request->device_token]);
            }

            $categories =  Cart::where('user_id', $request->user_id)->where('device_token', $request->device_token)->groupBy('category_id')->pluck('category_id');

            $product_array = [];
            $category_name = '';
            $total_price_product = 0;
            $total_add_on_service_prices = 0;
            foreach ($categories as $key =>  $category) {
                $product_data = [];
                $category_name =  Category::where('id', $category)->value('name');
                $cart_data  =  Cart::where('user_id', $request->user_id)->where('device_token', $request->device_token)->where('category_id', $category)->get();
                foreach ($cart_data as $key1 => $cart) {
                    $p_data = Product::find($cart->product_id);
                    $add_on_service_ids  = AddOnServiceMappingInCart::where(['cart_id' => $cart->id])->pluck('add_on_service_id')->join(',');

                    $add_on_service_arr  = AddOnService::select('id', 'price', 'title')->whereIn('id', explode(',', $add_on_service_ids))->get()->toArray();
                    $add_on_service_price = array_sum(array_column($add_on_service_arr, 'price')) * $cart->product_quantity;
                    $total_add_on_service_prices = $total_add_on_service_prices + $add_on_service_price;
                    $p_image        =  asset('storage/app/public/product_images/' . $p_data->image);
                    $product_data[$key1] = ['cart_id' => $cart->id, 'product_id' => $p_data->id, 'product_name' => $p_data->name, 'product_image' => $p_image, 'per_product_price' => $p_data->price, 'product_total_per' => $p_data->price * $cart->product_quantity, 'product_quantity' => $cart->product_quantity, 'category_id' => $category, 'add_on_services' => $add_on_service_arr, 'add_on_service_price' => $add_on_service_price];
                    $total_price_product = $total_price_product + $p_data->price * $cart->product_quantity;
                }
                $product_array[$key]['category_name'] = $category_name;
                $product_array[$key]['product'] = $product_data;
            }
            // $total_add_on_service_ids   = AddOnServiceMappingInCart::where(['user_id' => $request->user_id])->get('add_on_service_id');

            // $total_add_on_service_price = 0;
            // foreach ($total_add_on_service_ids as $key => $value2) {
            //     $serice = AddOnService::find($value2->add_on_service_id);
            //     $total_add_on_service_price = $total_add_on_service_price + $serice->price;
            // }

            $total_sum = 0;
            $get_cart_data1  = Cart::where(['user_id' => $request->user_id])->where('device_token', $request->device_token)->get();
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
            $checkExist  = ZipCode::where('zipcode', @$get_address->pincode)->first();
            $discount            = 0;
            $discount_percentage = CouponCartMapping::where(['user_id' => $request->user_id])->where('device_token', $request->device_token)->value('discount_percentage');
            $max_discount_amount = CouponCartMapping::where(['user_id' => $request->user_id])->where('device_token', $request->device_token)->value('max_discount_amount');
            $discount            = round(($total_sum + $total_add_on_service_prices) / 100 * $discount_percentage, 2);

            if ($discount >= $max_discount_amount) {
                $discount = $max_discount_amount;
            }

            if (!isset($discount) || $discount == '') {
                $discount = 0;
            }
            $coupon_code = CouponCartMapping::where(['user_id' => $request->user_id])->where('device_token', $request->device_token)->value('coupon_code');
            if (!isset($coupon_code) || $coupon_code == '') {
                $coupon_code = '';
            }
            $delivery_charge                = isset($checkExist->delivery_charge) ? $checkExist->delivery_charge : 0;
            $minimum_order_value            = isset($checkExist->minimum_order_value) ? $checkExist->minimum_order_value : 0;
            $total_product_and_service_amt  = $total_sum + $total_add_on_service_prices;
            if ($total_product_and_service_amt >= $minimum_order_value) {
                $delivery_charge = 0;
            }
            if($total_product_and_service_amt == 0){
                $delivery_charge = 0;

            }
            $total_amount = $delivery_charge + $total_sum + $total_add_on_service_prices - $discount;
            return $this->sendSuccess('TIMESLOT GET SUCCESSFULLY', ['sloat' => TimeSlotResource::collection($cagetory_list), 'add_on_service_charge' => $total_add_on_service_prices, 'delivery_charge' => $delivery_charge, 'sub_total' => $total_price_product, 'coupon_discount' => $discount, 'coupon_code' => $coupon_code, 'total_order_sum' => $total_amount, 'address' => $address]);
        } catch (\Throwable $e) {
            return $this->sendFailed($e->getMessage() . ' on line ' . $e->getLine(), 400);
        }
    }
}
