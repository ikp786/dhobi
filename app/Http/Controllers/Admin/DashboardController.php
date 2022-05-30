<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function index()
    {
        $title              = 'Dashboard';
        $totalUser          = User::where('role', 2)->count();
        $totalDriver        = User::where('role', 3)->count();
        $totalOrder         = Order::count();
        $todayOrder         = Order::whereDate('created_at', Carbon::today())->count();
        $data     = compact('title','totalUser','totalDriver','totalOrder','todayOrder');
        return view('admin.dashboard', $data);
    }

    public function logOut()
    {
        Auth::logout();
        return redirect()->route('admin')->with('success', 'logout success.');
    }
    public function userList()
    {
        $title      = 'User list';
        $users      = User::where('role', 2)->get();
        $data       = compact('title', 'users');
        return view('admin.users.index', $data);
    }

    public function settingList()
    {
        $title      = 'Setting List';
        $settings   = Setting::first();
        $data       = compact('title', 'settings');
        return view('admin.settings.index', $data);
    }

     /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function settingEdit($id)
    {
        $title      = 'Edit Setting';
        $setting   = Setting::find($id);
        $data       = compact('title','setting');        
        return view('admin.settings.edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function settingUpdate(Request $request, $id)
    {
        // dd($request->all());
        $delivery      = Setting::find($id);
        $delivery->fill($request->only('deliver_charge'));
        $delivery->save();
        return redirect()->route('admin.settings.index')->with('success', 'Setting update success.');

    }
}
