<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddOnService;
use App\Http\Requests\StoreAddOnServiceRequest;
use App\Http\Requests\UpdateAddOnServiceRequest;

class AddOnServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title           = 'Add On Service List';
        $add_on_services = AddOnService::OrderBy('id','desc')->get();        
        $data            = compact('title', 'add_on_services');
        return   view('admin.add_on_services.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title   = 'Add On Service';
        $data    = compact('title');
        return view('admin.add_on_services.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAddOnServiceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAddOnServiceRequest $request)
    {
        $add_on_services    = new AddOnService();
        $add_on_services->fill($request->only('title', 'price', 'status'));
        $add_on_services->save();
        return redirect()->route('admin.add_on_services.index')->with('success', 'Add On Service added success.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AddOnService  $AddOnService
     * @return \Illuminate\Http\Response
     */
    public function show(AddOnService $AddOnService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AddOnService  $AddOnService
     * @return \Illuminate\Http\Response
     */
    public function edit(AddOnService $AddOnService, $id)
    {
        $title           = 'Edit Add On Service';
        $add_on_services = AddOnService::find($id); //$AddOnService;
        $data            = compact('title', 'add_on_services');
        return view('admin.add_on_services.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAddOnServiceRequest  $request
     * @param  \App\Models\AddOnService  $AddOnService
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAddOnServiceRequest $request, $id)
    {
        $add_on_services    = AddOnService::find($id);
        $add_on_services->fill($request->only('title', 'price', 'status'));
        $add_on_services->save();
        return redirect()->route('admin.add_on_services.index')->with('success', 'Updates success.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AddOnService  $AddOnService
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $add_on_services = AddOnService::find($id)->delete();
        return redirect()->route('admin.add_on_services.index')->with('success', 'deleted  success.');
    }
}
