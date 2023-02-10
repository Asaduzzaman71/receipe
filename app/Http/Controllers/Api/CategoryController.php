<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Traits\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class CategoryController extends Controller
{
    use FileUpload;
    public function index()
    {
        try {
            $categories = Category::get()->latest();
            return response()->json([
                'categories' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required||unique:users|string|between:2,100|unique:categories',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
            if($request->hasFile('image')){
                $image = $this->FileUpload($request->image,'images');
            }
            $newCategory = Category::create([
                'name' => $request->name,
                'image' => isset($image) ? $image : null,
            ]);
            return response()->json([
                'message' => 'Category created successfully',
                'category' => $newCategory,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

   

    public function show( $id)
    {
        try {
            $category = Category::whereId($id)->first();
            return response()->json([
                'category' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function update(Request $request, $id){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required||unique:users|string|between:2,100',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
          
            $category = Category::whereId($id)->first();
            $category->name = $request->name;
            if($request->hasFile('image')){
                Storage::delete($category->image);
                $image = $this->FileUpload($request->image,'images');
                $category->image = $image;
            }
            $category->save();
            return response()->json([
                'category' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    public function destroy( $id)
    {
        try {
          
            $category = Category::whereId($id)->first();
            Storage::delete($category->image);
            $category->delete();
            return response()->json([
                'message' => 'category deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
