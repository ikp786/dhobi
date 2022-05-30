<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use App\Http\Requests\StoreTimeSlotRequest;
use App\Http\Requests\UpdateTimeSlotRequest;
use App\Models\TimeSlot;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title  = 'Time Slot';

        $timeslots = TimeSlot::get();

        return view('admin.timeslots.index', compact('timeslots', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title  = 'Time Slot';
        $data   = compact('title');
        return view('admin.timeslots.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimeSlotRequest $request)
    {

        $input = $request->validated();
        
        if (isset($request->status) && $request->status == 1) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
        }
        $TimeSlot = TimeSlot::create($input);
        return redirect()->route('admin.timeslots.index')->with('success', 'New TimeSlot Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(Gate::denies('TimeSlot_show'), Response::HTTP_FORBIDDEN, 'Forbidden');

        // return view('admin.timeslots.show',compact('timeslots'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title  = 'Time Slot';
        $timeslots = TimeSlot::find($id);
        $data   = compact('title', 'timeslots');
        return view('admin.timeslots.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeSlotRequest $request, $id)
    {
        $input = $request->all();
        $TimeSlot = TimeSlot::find($id);
        if (isset($request->status) && $request->status == 1) {
            $input['status'] = 1;
        } else {
            $input['status'] = 0;
        }
        $TimeSlot->update($input);
        return redirect()->route('admin.timeslots.index')->with('success', 'TimeSlot Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $TimeSlot = TimeSlot::find($id);
        $TimeSlot->delete();
        return redirect()->back()->with(['success' => "TimeSlot Deleted"]);
    }

    public function restore($id)
    {
        $TimeSlot = TimeSlot::onlyTrashed()->find($id);
        $TimeSlot->restore();
        return redirect()->back()->with(['success' => "TimeSlot restored."]);
    }

    public function delete($id)
    {
        $TimeSlot = TimeSlot::onlyTrashed()->find($id);
        $TimeSlot->forceDelete();
        return redirect()->back()->with(['success' => "TimeSlot Deleted"]);
    }

    public function chageStatus(Request $request)
    {
        if (request()->ajax()) {
            $TimeSlot = TimeSlot::find($request->id);
            $TimeSlot->status = $request->status;
            $TimeSlot->save();
            return response()->json(['success' => ' status change successfully.']);
        }
    }
}
