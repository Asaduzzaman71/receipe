<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Traits\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class CategoryController extends Controller
{
    use FileUpload;
    public function index()
    {
        try {
            $categories = Category::select('name','image')->latest()->all();
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
                'name' => 'required||unique:users|string|between:2,100',
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
    
    public function update(Request $request, $id)
    {
        //save the edited post
    }

    
    public function destroy( $id)
    {
        //delete a post
    }
}
