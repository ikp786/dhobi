<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeliveryBoyRequest;
use App\Http\Requests\UpdateDeliveryBoyRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeliveryBoyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title      = 'list Delivery Boy';
        $delivery   = User::where('role', 3)->get();
        $data       = compact('title', 'delivery');
        return view('admin.delivery_boys.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title  = 'addd Delivery Boy';
        $data   = compact('title');
        return view('admin.delivery_boys.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeliveryBoyRequest $request)
    {
        $input              = $request->validated();
        $input['role']      = 3;
        $input['password']  = Hash::make($request->password);
        $user = User::create($input);
        $user->unique_id = $user->id . rand(10, 99);
        $user->save();
        $user->assignRole([$input['role']]);
        return redirect()->route('admin.delivery-boys.index')->with('success', 'Delivery Boys added success.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title      = 'show Delivery boy';
        $delivery   = User::find($id);
        $data       = compact('title','delivery');
        return view('admin.delivery_boys.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title      = 'Edit Delivery boy';
        $delivery   = User::find($id);
        $data       = compact('title','delivery');        
        return view('admin.delivery_boys.edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDeliveryBoyRequest $request, $id)
    {
        $delivery      = User::find($id);
        $delivery->fill($request->all());
        if($request->has('password')){
            $delivery->password = Hash::make($request->password);
        }
        $delivery->save();
        return redirect()->route('admin.delivery-boys.index')->with('success', 'Delivery Boys update success.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delivery  = User::find($id)->delete();
        return redirect()->route('admin.delivery-boys.index')->with('success', 'Delivery Boys deleted success.');

    }

    public function changeStaus(Request $request)
	{
		if (request()->ajax()) {
			$category = User::find($request->id);
			$category->status = $request->status;
			$category->save();
			return response()->json(['success' => ' status change ' . $request->status . ' successfully.']);
		}
	}
}
