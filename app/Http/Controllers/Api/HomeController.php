<?php

namespace App\Http\Controllers\Api;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Bookmark;
use App\Models\Tutorial;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $blogs = Blog::latest()->take(5)->get();
            $categories = Category::latest()->take(5)->get();
            $tutorials = Tutorial::with('tutorialImages')->latest()->take(5)->get();
             $bookmarkedTutorials = Bookmark::pluck('tutorial_id')->toArray();
           foreach($tutorials  as $tutorial){
                if(in_array($tutorial->id,$bookmarkedTutorials)){
                    $tutorial->is_bookmarked =  true;
                }else{
                    $tutorial->is_bookmarked =  false;
                }
           }
            return response()->json([
                'blogs' => $blogs,
                'categories' => $categories,
                'tutorials' => $tutorials,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function tutorialsWithBookmarksinfo()
    {
        try {
           $tutorials = Tutorial::select('id','title','description','video_length', 'calorie')->with('tutorialImages')->get();
           $bookmarkedTutorials = Bookmark::pluck('tutorial_id')->toArray();
           foreach($tutorials  as $tutorial){
                if(in_array($tutorial->id,$bookmarkedTutorials)){
                    $tutorial->is_bookmarked =  true;
                }else{
                    $tutorial->is_bookmarked =  false;
                }
           }
            return response()->json([
                'tutorials' => $tutorials,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
