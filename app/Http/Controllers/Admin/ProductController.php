<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\AddOnService;
use App\Models\AddOnServiceMappingInProduct;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products   = Product::with('categories', 'sub_categories')->orderBy('id', 'desc');
        if (isset($request->category_id) &&  $request->category_id != '') {
            $products->where('category_id', $request->category_id);
        }
        if (isset($request->status) &&  $request->status != '') {
            $products->where('status', $request->status);
        }
        $products = $products->get();
        $categories  = Category::pluck('name', 'id');
        $title      = 'products';
        $data       = compact('title', 'products', 'request', 'categories');
        return view('admin.products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title              = 'products';
        $categories         = Category::pluck('name', 'id');
        $add_on_services    = AddOnService::pluck('title', 'id');
        $data         = compact('title', 'categories', 'add_on_services');
        return view('admin.products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        try {
            \DB::beginTransaction();
            $products      = new Product();
            $products->fill($request->all());
            if ($request->hasFile('image')) {
                $fileName = time() . '_' . str_replace(" ", "_", $request->image->getClientOriginalName());
                $request->file('image')->storeAs('product_images', $fileName, 'public');
                $products->image = $fileName;
            }
            $products->save();
            if (is_array($request->add_on_service)) {
                foreach ($request->add_on_service as $add_on_service) {
                    $add_on_services  = new AddOnServiceMappingInProduct();
                    $add_on_services->product_id = $products->id;
                    $add_on_services->add_on_service_id = $add_on_service;
                    // dd($add_on_services);
                    $add_on_services->save();
                }
            }
            \DB::commit();
            return redirect()->route('admin.products.index')->withSuccess('Product added success.');
        } catch (\Throwable $e) {
            \DB::rollback();
            return redirect()->back()->with('Failed', $e->getMessage() . ' on line ' . $e->getLine());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, $id)
    {
        $title           = 'products';
        $categories      = Category::pluck('name', 'id');
        $sub_categories  = SubCategory::pluck('name', 'id');
        $add_on_services = AddOnService::pluck('title', 'id');
        $products        = Product::with('addOnServiceMappingInProduct')->find($id);
        // dd($products->addOnServiceMappingInProduct[0]->add_on_service_id);
        $data            = compact('title', 'categories', 'products', 'sub_categories', 'add_on_services');
        return view('admin.products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product, $id)
    {
        try {
            \DB::beginTransaction();
            $products      = $product->find($id);
            $products->fill($request->only('name', 'price', 'category_id', 'sub_category_id', 'status'));
            if ($request->hasFile('image')) {
                $fileName = time() . '_' . str_replace(" ", "_", $request->image->getClientOriginalName());
                $request->file('image')->storeAs('product_images', $fileName, 'public');
                $products->image = $fileName;
            }
            $products->save();
            if (is_array($request->add_on_service)) {
                AddOnServiceMappingInProduct::where('product_id', $id)->delete();
                foreach ($request->add_on_service as $add_on_service) {
                    $add_on_services  = new AddOnServiceMappingInProduct();
                    $add_on_services->product_id = $products->id;
                    $add_on_services->add_on_service_id = $add_on_service;
                    // dd($add_on_services);
                    $add_on_services->save();
                }
            }
            \DB::commit();
            return redirect()->route('admin.products.index')->withSuccess('Product update success.');
        } catch (\Throwable $e) {
            \DB::rollback();
            return redirect()->back()->with('Failed', $e->getMessage() . ' on line ' . $e->getLine());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, $id)
    {
        try {
            \DB::beginTransaction();
            AddOnServiceMappingInProduct::where('product_id', $id)->delete();
            $product->find($id)->delete();
            \DB::commit();
        } catch (\Throwable $e) {
            \DB::rollback();
            return redirect()->back()->with('Failed', $e->getMessage() . ' on line ' . $e->getLine());
        }
        return redirect()->back()->withSuccess('Product delete success.');
    }

    public function deleteProductImage(Request $request)
    {
        if (request()->ajax()) {
            $doc = ProductImage::findOrfail($request->id);
            if (!empty($doc)) {
                if (Storage::disk('public')->exists('product_images/' . $doc->image)) {
                    Storage::disk('public')->delete('product_images/' . $doc->image);
                }
                $doc->delete();
                return response()->json(['status' => true]);
            }
            return response()->json(['status' => false]);
        }
    }
}
