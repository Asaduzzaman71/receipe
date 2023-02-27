<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Traits\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class BlogController extends Controller
{
    use FileUpload;
    public function index()
    {
        try {
            $blogs = Blog::all();
            return response()->json([
                'blogs' => $blogs,
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
                'name' => 'required|string|between:2,255|unique:blogs',
                'image' => 'required',
                'description'=>'required|string',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
            if($request->image){
                $base64_image = $request->image;
                $imageNameWithPath = $this->FileUpload($base64_image,'images');
            }
            $newBlog = Blog::create([
                'name' => $request->name,
                'image' => isset($imageNameWithPath) ? $imageNameWithPath : null,
                'description' => $request->description,
            ]);
            return response()->json([
                'message' => 'Blog created successfully',
                'blog' => $newBlog,
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
           $blog = Blog::whereId($id)->first();
            return response()->json([
                'blog' =>$blog,
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
                'name' => 'required|string|between:2,100',
                'image' => 'sometimes|string',
                'description'=>'required|string',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
          
            $blog = Blog::whereId($id)->first();
            $blog->name = $request->name;
            $blog->description = $request->description;
          
            if($request->image){
                $base64_image = $request->image;
                $imageNameWithPath = $this->FileUpload($base64_image,'images');
                $blog->image = $imageNameWithPath;
            }
           $blog->save();
            return response()->json([
                'blog' =>$blog,
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
          
            $blog = Blog::whereId($id)->first();
            Storage::delete($blog->image);
            $blog->delete();
            return response()->json([
                'message' => 'Blog deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
