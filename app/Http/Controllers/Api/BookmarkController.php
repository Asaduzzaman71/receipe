<?php

namespace App\Http\Controllers\Api;

use App\Models\Tutorial;
use App\Models\Bookmark;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class BookmarkController extends Controller
{
    public function index()
    {
        try {
            $bookmarks = Bookmark::all();
            return response()->json([
                'bookmarks' => $bookmarks,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

  
    

    public function store(Request $request){
        try{
            $newBookmark = Bookmark::create([
                'tutorial_id' => $request->tutorial_id,
                'user_id' => auth()->user()->id
            ]);
            return response()->json([
                'message' => 'Bookmark created successfully',
                'bookmark' => $newBookmark,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy( $id)
    {
        try {
          
            $bookmark = Bookmark::whereId($id)->first();
            $bookmark->delete();
            return response()->json([
                'message' => 'bookmark deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
