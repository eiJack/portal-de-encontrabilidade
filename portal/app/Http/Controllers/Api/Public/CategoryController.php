<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notice;

class CategoryController extends Controller
{
   
    public function home()
    {
        $categories = Category::select('id','name','slug')->with(
            [
                'notices' => function($query){
                    $query->select(
                        'id',
                        'category_id',
                        'title',
                        'description',
                        'path_image',
                        'slug',
                        'created_at'
                    )->latest();
                }
            ]
        )->latest()->get();

        return response()->json([
            "data" => $categories,
        ], 200);
    }

    public function list(){
         $categories = Category::select('id','name','slug')->latest()->get();
         return response()->json([
            "data" => $categories,
        ],200);
    }
    
    public function index(string $slug)
    {
        $category = Category::select('id', 'name', 'slug')
            ->where('slug', $slug)
            ->firstOrFail();

        $notices = Notice::select(
                'id',
                'category_id',
                'title',
                'description',
                'path_image',
                'slug',
                'created_at'
            )
            ->with('category:id,name,slug')
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'category' => $category,
            'notices' => $notices
        ], 200);
    }

}
