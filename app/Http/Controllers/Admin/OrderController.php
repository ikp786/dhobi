<?php

namespace App\Http\Controllers\Admin;

use App\Exports\NewOrderExport;
use App\Exports\OldOrderExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Support;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;
use Excel;

class OrderController extends Controller
{
    public function newOrderList(Request $request)
    {

        $order     =  Order::with('users', 'addresses', 'orderProductList')
            ->where('order_delivery_status', '!=', 'Deliver');

        if (isset($request->start_date) && !empty($request->start_date)) {
            $order->whereDate('created_at', '>=',  $request->start_date);
        }

        if (isset($request->end_date) && !empty($request->end_date)) {
            $order->whereDate('created_at', '<=',  $request->end_date);
        }

        $orders = $order->get();
        $drivers    =  User::where('role', 3)->where('name','!=',null)->get(['name', 'id', 'mobile']);
        $title      =  'Order';
        $data       =  compact('title', 'orders', 'drivers', 'request');
        return view('admin.orders.order-new', $data);
    }


    public function newOrderDownloadCsv(Request $request)
    {

        $order     =  Order::with('users', 'addresses', 'drivers', 'orderProductList')
            ->where('order_delivery_status', '!=', 'Deliver');

        if (isset($request->start_date) && !empty($request->start_date)) {
            $order->whereDate('created_at', '>=',  $request->start_date);
        }

        if (isset($request->end_date) && !empty($request->end_date)) {
            $order->whereDate('created_at', '<=',  $request->end_date);
        }
        $orders = $order->orderBy('id', 'desc'); //->get();

        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        'CLEANZI Order(01-06-2022).CSV';
        return Excel::download(new NewOrderExport($orders), 'CLEANZI Order('.date('Y-m-d') . ').csv');
    }

    public function oldOrderDownloadCsv(Request $request)
    {

        $order     =  Order::with('users', 'addresses', 'drivers', 'orderProductList')->where('order_delivery_status', 'Deliver');
        if (isset($request->start_date) && !empty($request->start_date)) {
            $order->whereDate('created_at', '>=',  $request->start_date);
        }
        if (isset($request->end_date) && !empty($request->end_date)) {
            $order->whereDate('created_at', '<=',  $request->end_date);
        }
        $orders = $order->whereNotNull('driver_id')->orderBy('id', 'desc'); //->get();

        // return Excel::download(new OldOrderExport($orders), date('Y-m-d') . '.csv');
        return Excel::download(new OldOrderExport($orders), 'CLEANZI Order('.date('Y-m-d') . ').csv');

    }

    public function oldOrderList(Request $request)
    {
        $order     =  Order::with('users', 'addresses', 'drivers', 'orderProductList')->where('order_delivery_status', 'Deliver');
        if (isset($request->start_date) && !empty($request->start_date)) {
            $order->whereDate('created_at', '>=',  $request->start_date);
        }
        if (isset($request->end_date) && !empty($request->end_date)) {
            $order->whereDate('created_at', '<=',  $request->end_date);
        }
        $orders = $order->whereNotNull('driver_id')->orderBy('id', 'desc')->get();
        $title      =  'Order';
        $data       =  compact('title', 'orders', 'request');
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


    public function generatePDFOrderDetail($id)
    {
        $orders     =  Order::where('id', $id)->with('orderProductList', 'orderProductList.products', 'addresses', 'users', 'drivers')->first();
        $categories =  OrderProduct::where('order_id', $id)->groupBy('category_id')->pluck('category_id');
        $product_data = [];
        foreach ($categories as $category) {
            $pcategory_name =  Category::where('id', $category)->value('name');
            $product_data[$pcategory_name]  =  OrderProduct::with('products')->where('order_id', $id)->where('category_id', $category)->get();
        }

        $title      =  'Orders';
        $data1       =  compact('title', 'orders', 'product_data');

        $data = ['title' => 'Welcome to jploft.com'];

        //$pdf = PDF::loadView('admin.orders.pdf', $data1)->setOptions([ 'isRemoteEnabled' => true,'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('admin.orders.pdf', $data1)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->download('CLEANZI Order('.date('Y-m-d') . ').pdf');
    }

    public function asignDriver(Request $request)
    {
        if (request()->ajax()) {
            $driver = User::find($request->driver_id);
            // if ($driver->online_status == "Online") {
            $order = Order::find($request->order_id);
            $order->driver_id = $request->driver_id;
            $order->save();
            return response()->json(['success' => 'driver asign successfully.', 'status' => true]);
            // } else {
            //     return response()->json(['error' => 'Sorry! this driver is  offline.', 'status' => false]);
            // }
        }
    }

    function userSupport(Request $request)
    {
        $title = 'Support';
        $supports = Support::with('users')->get();

        $data       =  compact('title', 'supports');
        return view('admin.supports.index', $data);
    }

    public function orderCancelAdmin(Request $request)
    {
            $order = Order::find($request->id);
            $order->admin_cancel_remark = $request->admin_cancel_remark;
            $order->order_delivery_status = "Canceled";
            $order->save();
            if ($order) {
                return response()->json(['success' => 'Update Successfully.', 'status' => 200]);
            }else{
                return response()->json(['success' => 'Failed.', 'status' => 999]);

            }
    }
}
