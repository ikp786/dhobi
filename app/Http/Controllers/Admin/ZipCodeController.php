<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZipCode;
use Illuminate\Http\Request;

class ZipCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title  = 'zipcodes';

        $zipcodes = ZipCode::get();

        return view('admin.zipcodes.index', compact('zipcodes', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title  = 'zipcodes';
        $data   = compact('title');
        return view('admin.zipcodes.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'zipcode'               => 'required|integer|digits:6',
            'delivery_charge'       => 'required|numeric|digits_between:1,8',
            'minimum_order_value'   => 'required|numeric|digits_between:1,8',
        ]);

        $input = $request->all();

        if (isset($request->status) && $request->status == 1) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
        }
        ZipCode::create($input);
        return redirect()->route('admin.zipcodes.index')->with('success', 'New zipcodes Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(Gate::denies('zipcodes_show'), Response::HTTP_FORBIDDEN, 'Forbidden');

        // return view('admin.zipcodes.show',compact('zipcodes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title  = 'zipcodes';
        $zipcodes = ZipCode::find($id);
        $data   = compact('title', 'zipcodes');
        return view('admin.zipcodes.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'zipcode'               => 'required|integer|digits:6',
            'minimum_order_value'   => 'required|numeric|digits_between:1,8',
            'delivery_charge'       => 'required|numeric|digits_between:1,8',
        ]);

        $input = $request->all();
        $zipcodes = ZipCode::find($id);
        if (isset($request->status) && $request->status == 1) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
        }
        $zipcodes->update($input);
        return redirect()->route('admin.zipcodes.index')->with('success', 'Zipcode Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $zipcodes = ZipCode::find($id);
        $zipcodes->delete();
        return redirect()->back()->with(['success' => "Zipcode Deleted"]);
    }

    public function restore($id)
    {
        $zipcodes = ZipCode::onlyTrashed()->find($id);
        $zipcodes->restore();
        return redirect()->back()->with(['success' => "Zipcode restored."]);
    }

    public function delete($id)
    {
        $zipcodes = ZipCode::onlyTrashed()->find($id);
        $zipcodes->forceDelete();
        return redirect()->back()->with(['success' => "Zipcode Deleted"]);
    }

    public function chageStatus(Request $request)
    {
        if (request()->ajax()) {
            $zipcodes = ZipCode::find($request->id);
            $zipcodes->status = $request->status;
            $zipcodes->save();
            return response()->json(['success' => ' status change successfully.']);
        }
    }
}
