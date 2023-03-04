<?php

namespace App\Http\Controllers\Api;

use App\Models\Tutorial;
use App\Models\TutorialImage;
use App\Models\TutorialStep;
use App\Traits\FileUpload;
use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
class TutorialController extends Controller
{
    use FileUpload;
    public function index()
    {
        try {
            $tutorials = Tutorial::select('id','title','description','video_length', 'calorie')->with('tutorialImages')->get();
            // $tutorials = Tutorial::with('tutorialImages')->select('title','description','is_premium','turotial_images:id');
            // $tutorials = Tutorial::join('tutorial_images', 'tutorials.id', '=', 'tutorial_images.tutorial_id')
            //    ->get(['tutorials.id','tutorials.title','tutorials.is_premium', 'tutorial_images.*']);
            return response()->json([
                'tutorials' => $tutorials,
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
                'title' => 'required||unique:tutorials|string|between:2,500',
                'category_id' => 'required|integer',
                'description' => 'required|string',
                'ingredients.*' => 'required|integer|distinct', 
                'steps.*' => 'required', 
                'images.*' => 'sometimes|string',
                'calorie' => 'string|nullable',
                'video_length' => 'string|nullable',
                'is_premium' => 'required|boolean',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
            if($request->video){
                $base64_video = $request->video;
                $videoNameWithPath = $this->FileUpload($base64_video,'videos');
            }
          
            $newtutorial = Tutorial::create([
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'ingredients' => $request->ingredients,
                'calorie' => $request->calorie,
                'video_length' => $request->video_length,
                'video' => isset($videoNameWithPath) ? $videoNameWithPath : null,
                'is_premium' => $request->is_premium
            ]);
         
            foreach( $request->steps as $step ){
                TutorialStep::create([
                    'tutorial_id' => $newtutorial->id,
                    'name' => $step['name'],
                    'description' => $step['description'],
                ]);
            }
     
            foreach($request->images as $image ){
                $imageNameWithPath = $this->FileUpload($image,'images');
                TutorialImage::create([
                    'tutorial_id' => $newtutorial->id,
                    'image' => $imageNameWithPath,
                ]);
            }
            return response()->json([
                'message' => 'Tutorial created successfully',
                'tutorial' => $newtutorial,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

   

    public function show( $id){
        try {
            $tutorial = Tutorial::with('tutorialSteps','tutorialImages')->whereId($id)->first();
            $ingredients = Ingredient::whereIn('id', json_decode($tutorial->ingredients))->get();
            $tutorial->ingredients = $ingredients;
            return response()->json([
                'tutorial' => $tutorial,
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
                'title' => 'required|string|between:2,500|unique:tutorials,title,'.$id,
                'category_id' => 'required|integer',
                'description' => 'required|string',
                'ingredients.*' => 'required|integer|distinct', 
                'steps.*' => 'required', 
                'images.*' => 'sometimes|string',
                'calorie' => 'string|nullable',
                'video_length' => 'string|nullable',
                'is_premium' => 'required|boolean',
            ]);
            if ($validator->fails()){
                return response()->json(array(
                'success' => false,
                'error' => $validator->getMessageBag()),
                400);
            }
            $tutorial = Tutorial::whereId($id)->first();
            $tutorial->title = $request->title;
            $tutorial->description = $request->description;
            $tutorial->ingredients = $request->ingredients;
            $tutorial->calorie = $request->calorie;
            $tutorial->video_length = $request->video_length;
            $tutorial->is_premium = $request->is_premium;
            if($request->video){
               $base64_video = $request->video;
               $videoNameWithPath = $this->FileUpload($base64_video,'videos');
               $tutorial->video = $videoNameWithPath;
            }
            $tutorial->save();
            TutorialStep::where('tutorial_id',$id)->delete();
            foreach( $request->steps as $step ){
                TutorialStep::create([
                    'tutorial_id' => $tutorial->id,
                    'name' => $step['name'],
                    'description' => $step['description'],
                ]);
            }
            if($request->images){
                $tutorialImages = TutorialImage::whereIn('id',json_decode($request->old_images_id))->get();
                foreach($tutorialImages as $tutorialImage){
                    Storage::disk('public')->delete($tutorialImage->image);
                    $tutorialImage->delete();
                }
                foreach($request->images as $image ){
                    $imageNameWithPath = $this->FileUpload($image,'images');
                    TutorialImage::create([
                        'tutorial_id' => $tutorial->id,
                        'image' => $imageNameWithPath,
                    ]);
                }
            }
            return response()->json([
                'tutorial' => $tutorial,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadVideo(Request $request){
            $tutorial = Tutorial::whereId($request->tutorial_id)->first();
            if($request->hasFile('video')){
                Storage::disk('public')->delete( $tutorial->video);
                $videoNameWithPath = $request->video->store('videos','public');
            }
            $tutorial->video = isset($videoNameWithPath) ? $videoNameWithPath : null;
            $tutorial->video_length = $request->video_length;
            $tutorial->save();
            return response()->json([
                'message' => 'tutorial video successfully'
            ], 200);
    }

    
    public function destroy( $id){
        try {
            $tutorial = Tutorial::whereId($id)->first();
            Storage::delete($tutorial->video);
            TutorialStep::where('tutorial_id',$id)->delete();
            $tutorialImages = TutorialImage::where('tutorial_id',$id)->get();
            foreach($tutorialImages as $tutorialImage){
                Storage::disk('public')->delete($tutorialImage->image);
                $tutorialImage->delete();
            }
            $tutorial->delete();
            return response()->json([
                'message' => 'tutorial deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
