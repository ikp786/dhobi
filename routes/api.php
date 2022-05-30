<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::fallback(function () {
    return response()->json([
        'ResponseCode'  => 404,
        'status'        => False,
        'message'       => 'URL not found as you looking'
    ]);
});

/*
        |--------------------------------------------------------------------------
        | USER DASHBOARD CHECK LOGIN OR NOT ROUTE
        |--------------------------------------------------------------------------
        */

Route::controller(ProductController::class)->group(function () {
    Route::post('user_dashboard', 'userDashboard');
    Route::get('ge_addonservice_by_product_id/{id}', 'getAddOnServiceByProductId');
});


if (last(explode("/", url()->current())) == 'user_dashboard') {
    if (auth('api')->check()) {
        Route::middleware('auth:api')->group(function () {
            Route::controller(ProductController::class)->group(function () {
                Route::get('user_dashboard', 'userDashboard');
            });
        });
    } else {
        Route::controller(ProductController::class)->group(function () {
            Route::post('user_dashboard', 'userDashboard');
        });
    }
}

/*
        |--------------------------------------------------------------------------
        | AUTHORISATION FAILED ROUTE
        |--------------------------------------------------------------------------
        */

Route::get('login', [AuthController::class, 'unauthorized_access'])->name('login');

/*
        |--------------------------------------------------------------------------
        | PRODUCT ROUTE
        |--------------------------------------------------------------------------
        */
Route::controller(ProductController::class)->group(function () {
    Route::get('get_category_list', 'getCategoryList');
    Route::get('get_sub_category_by_category/{id}', 'getSubCategoryByCategory');
    Route::get('get_offer_list', 'getOfferList');
    Route::post('get_product_category_wise', 'getProductCategoryWise');
    Route::get('get_product_fresh_farm', 'getProductFreshFarmList');
    Route::post('get_search_product', 'getSearchProduct');
    Route::get('get_product_detail/{id}', 'getProductDetails');
    Route::get('get_time_slot', 'getTimeSlot');
});

/*
        |--------------------------------------------------------------------------
        | AUTH REGISTER LOGIN SENT LOGIN OTP ROUTE
        |--------------------------------------------------------------------------
        */
Route::controller(AuthController::class)->group(function () {
    Route::post('user_register', 'userRegister');
    Route::post('sent_register_otp', 'sentRegisterOtp');
    Route::post('register_otp_verify', 'registerOtpVerify');
    Route::post('register_resent_otp_verify', 'registerReSentOtp');
});


    /*
        |--------------------------------------------------------------------------
        | PRODUCT CART ROUTE
        |--------------------------------------------------------------------------
        */
        Route::controller(ProductController::class)->group(function () {
            Route::post('add_to_cart', 'addToCart');
            Route::post('delete_in_cart', 'deleteProdcutInCart');
            Route::post('get_Cart_detail', 'getCartDetail');
            Route::post('delete_add_on_service_in_cart', 'deleteAddOnServiceInCart');
            // Route::post('apply_coupon', 'applyCoupon');
        });
        
/*
        |--------------------------------------------------------------------------
        | AUTHORISATION ROUTE
        |--------------------------------------------------------------------------
        */

Route::middleware('auth:api')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('get_user_profile', 'getUserProfile');
        Route::get('get_driver_profile', 'getDriverProfile');
        Route::post('update_user_profile', 'updateUserProfile');
        Route::post('update_mobile', 'updateMobile');
        Route::post('re_sent_otp_mobile_update', 'reSentOtpMobileUpdate');
        Route::get('change_online_status', 'changeOnlineStatus');
    });

    
    /*
        |--------------------------------------------------------------------------
        | PRODUCT CART ROUTE
        |--------------------------------------------------------------------------
        */
        Route::controller(ProductController::class)->group(function () {            
            Route::get('delete_coupon', 'deleteCoupon');
            Route::post('apply_coupon', 'applyCoupon');
        });

    /*
        |--------------------------------------------------------------------------
        | ADDRESS ROUTE
        |--------------------------------------------------------------------------
        */
    Route::controller(AddressController::class)->group(function () {
        Route::post('add_address', 'create');
        Route::post('update_address', 'update');
        Route::get('get_address_list', 'index');
        Route::delete('delete_address/{id}', 'delete');
        Route::get('change_address_status/{id}', 'changeAddressStatus');
    });


    /*
        |--------------------------------------------------------------------------
        | ORDER ROUTE
        |--------------------------------------------------------------------------
        */
    Route::controller(OrderController::class)->group(function () {
        Route::post('create_order', 'createOrder');
        Route::post('order_reschedule', 'orderReschedule');
        Route::post('save_order_payment_response', 'savePaymentResponse');
        Route::post('cancel_order', 'cancelOrder');
        Route::get('get_user_order_list/{status}', 'getUserOrderList');
        Route::get('get_user_order_detail/{id}', 'getUserOrderDetail');
        Route::get('get_driver_order_list/{status}', 'getDriverOrderList');
        Route::get('get_driver_order_detail/{id}', 'getDriverOrderDetail');
        Route::post('create_feedback', 'createFeedback');
        Route::post('order_deliver_by_driver', 'orderDeliverByDriver');
        Route::post('order_pickup_by_driver', 'orderPickupByDriver');
        Route::get('driver_dashboard', 'driverDashboard');
        Route::post('user_support', 'userSupport');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('get_notification', 'getNotification');
    });
});
