<?php

namespace App\Http\Controllers\Api;

use App\Models\tutorial;
use App\Models\TutorialImage;
use App\Models\TutorialStep;
use App\Traits\FileUpload;
use App\Http\Controllers\Controller;
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
            $tutorials = Tutorial::latest()->get();
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
                'category_id' => 'required|string',
                'description' => 'required|string',
                'ingredients.*' => 'required|number|distinct', 
                'steps.*' => 'required', 
                'video' => 'sometimes|string',
                'images' => 'sometimes|string',
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
                'title' => $request->name,
                'description' => $request->description,
                'ingredients' => $request->ingredients,
                'video' => isset($videoNameWithPath) ? $videoNameWithPath : null,
                'is_premium' => $request->is_premium
            ]);
            foreach( $request->steps as $step ){
                TutorialStep::create([
                    'tutorial_id' => $newtutorial->id,
                    'name' => $step->name,
                    'description' => $step->description,
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
                'title' => 'required||unique:tutorials|string|between:2,500',
                'category_id' => 'required|string',
                'description' => 'required|string',
                'ingredients.*' => 'required|number|distinct', 
                'steps.*' => 'required', 
                'video' => 'sometimes|string',
                'images' => 'sometimes|string',
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
                    'name' => $step->name,
                    'description' => $step->description,
                ]);
            }
            if($request->images){
                TutorialImage::whereIn('tutorial_id',$request->old_images_id)->delete();
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

    public function videoUpload(Request $request, $id){
            $tutorial = Tutorial::whereId($id)->first();
            if($request->video){
                $base64_video = $request->video;
                $videoNameWithPath = $this->FileUpload($base64_video,'videos');
            }
            $tutorial->video = isset($videoNameWithPath) ? $videoNameWithPath : null;
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
