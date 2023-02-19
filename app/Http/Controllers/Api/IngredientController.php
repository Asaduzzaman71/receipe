<?php

namespace App\Http\Controllers\Api;

use App\Models\Ingredient;
use App\Traits\FileUpload;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class IngredientController extends Controller
{
    use FileUpload;
    public function index()
    {
        try {
            $ingredients = Ingredient::get()->latest();
            return response()->json([
                'ingredients' => $ingredients,
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
                'name' => 'required|string|between:2,500|unique:ingredients',
                'image' => 'required|string',
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
            $newIngredient = Ingredient::create([
                'name' => $request->name,
                'image' => isset($imageNameWithPath) ? $imageNameWithPath : null,
            ]);
            return response()->json([
                'message' => 'Ingredient created successfully',
                'ingredient' => $newIngredient,
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
            $ingredient = Ingredient::whereId($id)->first();
            return response()->json([
                'ingredient' => $ingredient,
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
                'name' => 'required|string|between:2,500',
                'image' => 'sometimes|string',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
          
            $ingredient = Ingredient::whereId($id)->first();
            $ingredient->name = $request->name;
            if($request->image){
                $base64_image = $request->image;
                $imageNameWithPath = $this->FileUpload($base64_image,'images');
                $ingredient->image = $imageNameWithPath;
            }
            $ingredient->save();
            return response()->json([
                'ingredient' => $ingredient,
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
          
            $ingredient = Ingredient::whereId($id)->first();
            Storage::delete($ingredient->image);
            $ingredient->delete();
            return response()->json([
                'message' => 'Ingredient deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
