<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Notice;

class NoticeController extends Controller
{
    public function show(string $slug){
        $notice = Notice::select(
                'id',
                'category_id',
                'title',
                'description',
                'notice',
                'path_image',
                'slug',
                'created_at'
        )->with('category:id,name,slug')
        ->where('slug',$slug)
        ->firstOrFail();

        return response()->json([
            'data' => $notice
        ],200);
    }
}
