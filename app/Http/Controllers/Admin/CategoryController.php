<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Prophecy\Call\Call;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title          = 'Category List';
        $categories     = Category::all();
        $data           = compact('title','categories');
        return   view('admin.categories.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title   = 'Add Category';
        $data    = compact('title');
        return view('admin.categories.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $categories    = new Category();
        $image = time() . '_' . rand(1111, 9999) . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->storeAs('categories', $image, 'public');
        $request['image'] = $image;
        $categories->fill($request->only('name', 'status'));
        $categories->image  = $image;
        $categories->save();
        return redirect()->route('admin.categories.index')->with('success','Category added success.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category,$id)
    {
        
        $title          = 'Edit Category';
        $categories     = Category::find($id);//$category;
        $data           = compact('title','categories');
        
        return view('admin.categories.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category, $id)
    {        
        $categories    = Category::find($id);
        $categories->fill($request->only('name', 'status'));
        if($request->has('image')){
        $image = time() . '_' . rand(1111, 9999) . '.' . $request->file('image')->getClientOriginalExtension();
        $request->file('image')->storeAs('categories', $image, 'public');             
        $categories->image  = $image;
    }
        $categories->save();

        return redirect()->route('admin.categories.index')->with('success','Updates success.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category,$id)
    {
        $categories = Category::find($id)->delete();
        return redirect()->route('admin.categories.index')->with('success','deleted  success.');
    }
}
