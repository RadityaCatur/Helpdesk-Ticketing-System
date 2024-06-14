<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use App\Services\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {                
        $categories = Category::orderBy('created_at', 'DESC')
            ->when($request->q, function($categories) {
               $categories = $categories->where('name', 'ILIKE', '%' . request()->q . '%');
            })->get();

        return new JsonResponse([
            'data' => $categories
        ]);        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|unique:categories,name'
        ]);

        $category = new Category([
            'name' => $request->name
        ]);
        $category->save();

        return response()->json([
            'message' => 'Category successfully created',
            'data' => $category
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::where('id', $id)->first();
        if(!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|min:3|unique:categories,name'
        ]);
        
        $category = Category::where('id', $id)->first();
        if(!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->update($request->all());

        return response()->json([
            'message' => 'Category successfully updated',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::where('id', $id)->first();
        if(!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();
        return response()->json([
            'message' => 'Category successfully deleted'
        ], 200);
    }
}
