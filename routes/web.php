<?php

use App\Http\Controllers\Admin\AddOnServiceController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryBoyController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Admin\ZipCodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::fallback(function () {
    return response()->json([
        'ResponseCode'  => 404,
        'status'        => False,
        'message'       => 'URL not found as you looking'
    ]);
});

Route::get('/', function () {
    return view('admin.login');
});

Route::group(['prefix' => 'admin'], function () {

    Route::get('/', function () {
        return view('admin.login');
    })->name('admin');
    Route::post('login', [AdminController::class, 'login'])->name('admin.login');
    Route::group(['middleware'  => 'admin'], function () {

        /*
|--------------------------------------------------------------------------
| DASHBOARD | SETTING
|--------------------------------------------------------------------------
*/
        Route::controller(DashboardController::class)->group(function () {
            Route::get('dashboard', 'index')->name('admin.dashboard');
            Route::get('logout', 'logout')->name('admin.logout');
            Route::group(['prefix' => 'users'], function () {
                Route::get('index', 'userList')->name('admin.users.index');
            });

            Route::group(['prefix' => 'settings'], function () {
                Route::get('/', 'settingList')->name('admin.settings.index');
                Route::get('edit/{id}', 'settingEdit')->name('admin.settings.edit');
                Route::PATCH('update/{id}', 'settingUpdate')->name('admin.settings.update');
            });
        });

        /*
|--------------------------------------------------------------------------
| ORDER USER SUPPORT
|--------------------------------------------------------------------------
*/
        Route::controller(OrderController::class)->group(function () {
            Route::group(['prefix' => 'orders'], function () {
                Route::get('new', 'newOrderList')->name('admin.orders.new');
                Route::get('old', 'oldOrderList')->name('admin.orders.old');
                Route::post('orders/asign-driver', 'asignDriver')->name('admin.orders.asign-driver');
                Route::get('orders/order-product/{id}', 'orderProduct')->name('admin.orders.order-product');
            });
            Route::get('supprots/index', 'userSupport')->name('admin.supports.index');
        });



        /*
|--------------------------------------------------------------------------
| REPORT
|--------------------------------------------------------------------------
*/
        Route::controller(ReportController::class)->group(function () {
            Route::group(['prefix' => 'reports'], function () {
                Route::get('daily-purchase-create', 'createDailyPurchaseReport')->name('admin.daily.purchase.reports.create');
                Route::get('daily-purchase-edit/{date}', 'editDailyPurchaseReport')->name('admin.daily.purchase.reports.edit');
                Route::get('daily-purchase-index', 'indexDailyPurchaseReport')->name('admin.daily.purchase.reports.index');
                Route::post('daily-purchase-store', 'storeDailyPurchaseReport')->name('admin.daily.purchase.reports.store');
                Route::get('daily-order-index', 'indexDailyOrderReport')->name('admin.daily.order.reports.index');
            });
        });
        /*
|--------------------------------------------------------------------------
| CATEGORIES CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(CategoryController::class)->group(function () {
            Route::group(['prefix' => 'categories'], function () {
                Route::get('index', 'index')->name('admin.categories.index');
                Route::get('create', 'create')->name('admin.categories.create');
                Route::post('store', 'store')->name('admin.categories.store');
                Route::get('edit/{id}', 'edit')->name('admin.categories.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.categories.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.categories.destroy');
            });
        });

        /*
|--------------------------------------------------------------------------
| SUB CATEGORIES CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(SubCategoryController::class)->group(function () {
            Route::group(['prefix' => 'subcategories'], function () {
                Route::get('index', 'index')->name('admin.subcategories.index');
                Route::get('create', 'create')->name('admin.subcategories.create');
                Route::post('store', 'store')->name('admin.subcategories.store');
                Route::get('edit/{id}', 'edit')->name('admin.subcategories.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.subcategories.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.subcategories.destroy');
                Route::post('sub_category_by_category', 'subCategoryByCategory')->name('admin.sub_category_by_category');
            });
        });



        /*
|--------------------------------------------------------------------------
| ADDONSERVICE CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(AddOnServiceController::class)->group(function () {
            Route::group(['prefix' => 'add_on_services'], function () {
                Route::get('index', 'index')->name('admin.add_on_services.index');
                Route::get('create', 'create')->name('admin.add_on_services.create');
                Route::post('store', 'store')->name('admin.add_on_services.store');
                Route::get('edit/{id}', 'edit')->name('admin.add_on_services.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.add_on_services.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.add_on_services.destroy');
            });
        });

        /*
|--------------------------------------------------------------------------
| PRODUCTS CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(ProductController::class)->group(function () {
            Route::group(['prefix' => 'products'], function () {
                Route::get('index', 'index')->name('admin.products.index');
                Route::get('create', 'create')->name('admin.products.create');
                Route::post('store', 'store')->name('admin.products.store');
                Route::get('edit/{id}', 'edit')->name('admin.products.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.products.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.products.destroy');
                Route::delete('products/delete-image', 'deleteProductImage')->name('admin.products.delete-product-image');
                Route::post('add_fresh_farma', 'addFreshFarma')->name('admin.products.add_fresh_farma');
            });
        });


        /*
|--------------------------------------------------------------------------
| OFFERS CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(OfferController::class)->group(function () {
            Route::group(['prefix' => 'offers'], function () {
                Route::get('index', 'index')->name('admin.offers.index');
                Route::get('create', 'create')->name('admin.offers.create');
                Route::post('store', 'store')->name('admin.offers.store');
                Route::get('edit/{id}', 'edit')->name('admin.offers.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.offers.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.offers.destroy');
            });
        });


        /*
|--------------------------------------------------------------------------
| DELIVERY BOY CREATE STORE DELETE UPDATE EDIT 
|--------------------------------------------------------------------------
*/
        Route::controller(DeliveryBoyController::class)->group(function () {
            Route::group(['prefix' => 'delivery-boys'], function () {
                Route::get('index', 'index')->name('admin.delivery-boys.index');
                Route::get('create', 'create')->name('admin.delivery-boys.create');
                Route::post('store', 'store')->name('admin.delivery-boys.store');
                Route::get('edit/{id}', 'edit')->name('admin.delivery-boys.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.delivery-boys.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.delivery-boys.destroy');
                Route::post('status-change', 'changeStaus')->name('admin.user_status_change');
            });
        });


        /*
|--------------------------------------------------------------------------
| SLIDER CREATE STORE DELETE UPDATE EDIT
|--------------------------------------------------------------------------
*/
        Route::controller(SliderController::class)->group(function () {
            Route::group(['prefix' => 'sliders'], function () {
                Route::get('index', 'index')->name('admin.sliders.index');
                Route::get('create', 'create')->name('admin.sliders.create');
                Route::post('store', 'store')->name('admin.sliders.store');
                Route::get('edit/{id}', 'edit')->name('admin.sliders.edit');
                Route::PATCH('update/{id}', 'update')->name('admin.sliders.update');
                Route::delete('destroy{id}', 'destroy')->name('admin.sliders.destroy');
            });
        });

        /*
|--------------------------------------------------------------------------
| TIMESLOT CREATE STORE DELETE UPDATE EDIT
|--------------------------------------------------------------------------
*/
Route::controller(TimeSlotController::class)->group(function () {
    Route::group(['prefix' => 'timeslots'], function () {
        Route::get('index', 'index')->name('admin.timeslots.index');
        Route::get('create', 'create')->name('admin.timeslots.create');
        Route::post('store', 'store')->name('admin.timeslots.store');
        Route::get('edit/{id}', 'edit')->name('admin.timeslots.edit');
        Route::PATCH('update/{id}', 'update')->name('admin.timeslots.update');
        Route::delete('destroy{id}', 'destroy')->name('admin.timeslots.destroy');
    });
});


/*
|--------------------------------------------------------------------------
| ZIPCODE CREATE STORE DELETE UPDATE EDIT
|--------------------------------------------------------------------------
*/
Route::controller(ZipCodeController::class)->group(function () {
    Route::group(['prefix' => 'zipcodes'], function () {
        Route::get('index', 'index')->name('admin.zipcodes.index');
        Route::get('create', 'create')->name('admin.zipcodes.create');
        Route::post('store', 'store')->name('admin.zipcodes.store');
        Route::get('edit/{id}', 'edit')->name('admin.zipcodes.edit');
        Route::PATCH('update/{id}', 'update')->name('admin.zipcodes.update');
        Route::delete('destroy{id}', 'destroy')->name('admin.zipcodes.destroy');
    });
});



/*
|--------------------------------------------------------------------------
| COUPON CREATE STORE DELETE UPDATE EDIT
|--------------------------------------------------------------------------
*/
Route::controller(CouponController::class)->group(function () {
    Route::group(['prefix' => 'coupons'], function () {
        Route::get('index', 'index')->name('admin.coupons.index');
        Route::get('create', 'create')->name('admin.coupons.create');
        Route::post('store', 'store')->name('admin.coupons.store');
        Route::get('edit/{id}', 'edit')->name('admin.coupons.edit');
        Route::PATCH('update/{id}', 'update')->name('admin.coupons.update');
        Route::delete('destroy{id}', 'destroy')->name('admin.coupons.destroy');
    });
});





    });
});
