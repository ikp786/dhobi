<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title          = 'Sub Category List';
        $categories     = SubCategory::with('category')->get();
        // dd($categories);
        $data           = compact('title', 'categories');
        return   view('admin.subcategories.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title   = 'Add Sub Category';
        $categories = Category::pluck('name', 'id');
        $data    = compact('title', 'categories');
        return view('admin.subcategories.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubCategoryRequest $request)
    {
        $categories    = new SubCategory();
        $categories->fill($request->only('name', 'status', 'category_id'));
        $categories->save();
        return redirect()->route('admin.subcategories.index')->with('success', 'Sub Category added success.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(SubCategory $category)
    {
        //
    }

    public function subCategoryByCategory(Request $request)
    {
        // echo $request->category_id;die;
        $data['category'] = SubCategory::where("category_id", $request->category_id)
            ->get(["name", "id"]);
            // dd($data);

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCategory $category, $id)
    {
        $category       = Category::pluck('name', 'id');
        $title          = 'Edit Sub Category';
        $categories     = SubCategory::find($id); //$category;
        $data           = compact('title', 'categories', 'category');

        return view('admin.subcategories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubCategoryRequest $request, $id)
    {
        $categories    = SubCategory::find($id);
        $categories->fill($request->only('name', 'category_id', 'status'));
        $categories->save();

        return redirect()->route('admin.subcategories.index')->with('success', 'Updates success.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubCategory $category, $id)
    {
        $categories = SubCategory::find($id)->delete();
        return redirect()->route('admin.subcategories.index')->with('success', 'deleted  success.');
    }
}
