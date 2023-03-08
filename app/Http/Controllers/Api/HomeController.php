<?php

namespace App\Http\Controllers\Api;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Tutorial;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $blogs = Blog::latest()->take(5)->get();
            $categories = Category::latest()->take(5)->get();
            $tutorials = Tutorial::latest()->take(5)->get();
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

}
