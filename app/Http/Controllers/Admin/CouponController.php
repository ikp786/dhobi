<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title  = 'coupon';

        $coupons = Coupon::get();

        return view('admin.coupons.index', compact('coupons', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title  = 'coupons';
        $data   = compact('title');
        return view('admin.coupons.create', $data);
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
            'coupon_code'               => 'required|max:15',
            'minimum_order_amount'      => 'required|numeric',
            'max_discount_amount'      => 'required|numeric',
            'discount_percentage'       => 'required|integer',
            'start_date'                => 'required|date_format:Y-m-d|after_or_equal:today',
            'end_date'                  => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'description'               => 'required|',
        ]);

        $input = $request->all();

        if (isset($request->status) && $request->status == 1) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
        }
        Coupon::create($input);
        return redirect()->route('admin.coupons.index')->with('success', 'New coupons Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(Gate::denies('coupons_show'), Response::HTTP_FORBIDDEN, 'Forbidden');

        // return view('admin.coupons.show',compact('coupons'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title  = 'coupons';
        $coupons = Coupon::find($id);
        $data   = compact('title', 'coupons');
        return view('admin.coupons.edit', $data);
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
            'coupon_code'               => 'required|max:15',
            'minimum_order_amount'      => 'required|numeric',
            'max_discount_amount'      => 'required|numeric',
            'discount_percentage'       => 'required|integer',
            'start_date'                => 'required|date_format:Y-m-d|after_or_equal:today',
            'end_date'                  => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'description'               => 'required|',
        ]);

        $input = $request->all();
        $coupons = Coupon::find($id);
        if (isset($request->status) && $request->status == 1) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
        }
        $coupons->update($input);
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupons = Coupon::find($id);
        $coupons->delete();
        return redirect()->back()->with(['success' => "Coupon Deleted"]);
    }

    public function restore($id)
    {
        $coupons = Coupon::onlyTrashed()->find($id);
        $coupons->restore();
        return redirect()->back()->with(['success' => "Coupon restored."]);
    }

    public function delete($id)
    {
        $coupons = Coupon::onlyTrashed()->find($id);
        $coupons->forceDelete();
        return redirect()->back()->with(['success' => "Coupon Deleted"]);
    }

    public function chageStatus(Request $request)
    {
        if (request()->ajax()) {
            $coupons = Coupon::find($request->id);
            $coupons->status = $request->status;
            $coupons->save();
            return response()->json(['success' => ' status change successfully.']);
        }
    }
}
