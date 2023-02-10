<?php

namespace App\Http\Controllers\Api;

use App\Models\SubCategory;
use App\Traits\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    use FileUpload;
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         try{
            $validator = Validator::make($request->all(), [
                'category'=>'required',
                'name' => 'required||unique:users|string|between:2,100|unique:subcategories',
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
            $newSubCategory = SubCategory::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'image' => isset($image) ? $image : null,
            ]);
            return response()->json([
                'message' => 'SubCategory created successfully',
                'category' => $newSubCategory,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function show(SubCategory $id)
    {
         try {
            $subCategory = SubCategory::whereId($id)->first();
            return response()->json([
                'subCategory' => $subCategory,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(SubCategory $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required',
                'name' => 'required||unique:users|string|between:2,100',
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
            $subCategory = SubCategory::whereId($id)->first();
          
            if($request->hasFile('image')){
                Storage::delete($subCategory->image);
                $image = $this->FileUpload($request->image,'images');
                $subCategory->image = $image;
            }
            $subCategory->category_id = $request->category_id;
            $subCategory->name = $request->name;
            $subCategory->save();
            return response()->json([
                'subCategory' => $subCategory,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubCategory  $subCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         try {
            $subCategory = SubCategory::whereId($id)->first();
            Storage::delete($subCategory->image);
            $subCategory->delete();
            return response()->json([
                'message' => 'subcategory deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
