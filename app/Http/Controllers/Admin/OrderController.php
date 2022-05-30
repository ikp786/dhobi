<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Support;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function newOrderList()
    {
        $orders     =  Order::with('users', 'addresses', 'orderProductList')->where('order_delivery_status', '!=', 'Deliver')->orderBy('id', 'desc')->get();
        $drivers    =  User::where('role', 3)->get(['name', 'id', 'mobile']);
        $title      =  'Order';
        $data       =  compact('title', 'orders', 'drivers');
        return view('admin.orders.order-new', $data);
    }

    public function oldOrderList()
    {
        $orders     =  Order::with('users', 'addresses', 'drivers', 'orderProductList')->where('order_delivery_status', 'Deliver')->whereNotNull('driver_id')->orderBy('id', 'desc')->get();
        $title      =  'Order';
        $data       =  compact('title', 'orders');
        return view('admin.orders.order-old', $data);
    }

    public function orderProduct($id)
    {
        $orders     =  Order::where('id', $id)->with('orderProductList', 'orderProductList.products', 'addresses', 'users', 'drivers')->first();
        $categories =  OrderProduct::where('order_id', $id)->groupBy('category_id')->pluck('category_id');
        $product_data = [];
        foreach ($categories as $category) {
            $pcategory_name =  Category::where('id', $category)->value('name');
            $product_data[$pcategory_name]  =  OrderProduct::with('products')->where('order_id', $id)->where('category_id', $category)->get();
        }
        // dd($product_data);
        // dd($orders);
        $title      =  'Orders';
        $data       =  compact('title', 'orders', 'product_data');
        return view('admin.orders.show', $data);
    }

    public function asignDriver(Request $request)
    {
        if (request()->ajax()) {
            $driver = User::find($request->driver_id);
            if ($driver->online_status == "Online") {
                $order = Order::find($request->order_id);
                $order->driver_id = $request->driver_id;
                $order->save();
                return response()->json(['success' => 'driver asign successfully.', 'status' => true]);
            } else {
                return response()->json(['error' => 'Sorry! this driver is  offline.', 'status' => false]);
            }
        }
    }



    function userSupport(Request $request)
    {
        $title = 'Support';
        $supports = Support::with('users')->get();
        
        $data       =  compact('title', 'supports');
        return view('admin.supports.index', $data);
    }
}
