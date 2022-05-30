<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Http\Requests\StoreOfferRequest;
use App\Http\Requests\UpdateOfferRequest;
use App\Models\Product;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title    = 'offer';
        $offers  = Offer::with('products','products.images')->get();        
        $data     = compact('title', 'offers');
        return view('admin.offers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title      = 'offer';
        $products   = Product::pluck('name', 'id');
        $data       = compact('title', 'products');
        return view('admin.offers.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOfferRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOfferRequest $request)
    {
        $input = $request->validated();
        Offer::create($input);
        return redirect()->route('admin.offers.index')->with('Success', 'Offers added success.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Offer  $Offer
     * @return \Illuminate\Http\Response
     */
    public function show(Offer $Offer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Offer  $Offer
     * @return \Illuminate\Http\Response
     */
    public function edit(Offer $Offer, $id)
    {
        $title          = 'Edit Offer';
        $products       = Product::pluck('name', 'id');
        $offers         = Offer::find($id);
        $data           = compact('title', 'offers', 'products');

        return view('admin.offers.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOfferRequest  $request
     * @param  \App\Models\Offer  $Offer
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOfferRequest $request, Offer $Offer, $id)
    {
        $offers    = Offer::find($id);
        $offers->fill($request->only('product_id', 'minimum_order_value', 'quantity_type', 'description', 'status'));
        $offers->save();
        return redirect()->route('admin.offers.index')->with('success', 'Updates success.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Offer  $Offer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $Offer, $id)
    {
        Offer::findOrFail($id)->delete();
        return redirect()->route('admin.offers.index')->with('success', 'deleted  success.');
    }
}
