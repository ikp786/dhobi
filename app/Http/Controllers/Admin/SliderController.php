<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Http\Requests\StoreSliderRequest;
use App\Http\Requests\UpdateSliderRequest;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title    = 'slider';
        $sliders  = Slider::all();
        $data     = compact('title', 'sliders');
        return view('admin.sliders.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title    = 'slider';
        $data     = compact('title');
        return view('admin.sliders.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSliderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSliderRequest $request)
    {
        $input = $request->validated();
        if ($request->image) {
            $fileName = time() . '_' . str_replace(" ", "_", $request->image->getClientOriginalName());
            $request->file('image')->storeAs('sliders_images', $fileName, 'public');
            $input['image'] = $fileName;
        }
        Slider::create($input);
        return redirect()->route('admin.sliders.index')->with('Success', 'Sliders added success.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider, $id)
    {
        $title          = 'Edit Slider';
        $sliders        = Slider::find($id);
        $data           = compact('title', 'sliders');

        return view('admin.sliders.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSliderRequest  $request
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSliderRequest $request, Slider $slider, $id)
    {
        $sliders    = Slider::find($id);
        $sliders->fill($request->only('title'));
        if ($request->has('image')) {
            $image = time() . '_' . rand(1111, 9999) . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('sliders_images', $image, 'public');
            $sliders->image  = $image;
        }
        $sliders->save();

        return redirect()->route('admin.sliders.index')->with('success', 'Updates success.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Slider $slider, $id)
    {
       Slider::findOrFail($id)->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'deleted  success.');
    }
}
