<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notices = Notice::with('category')
            ->whereHas('category', function($query){
            $query->where('user_id', auth()->id());
        })->latest()->get();

        return response()->json([
            'data' => $notices
        ],200);
    }

      /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'notice' => ['required', 'string'],
            'path_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $category = Category::findOrFail($validated['category_id']);


        abort_if($category->user_id !== auth()->id(), 403, 'Acesso negado.');

        $imagePath = null;

        if ($request->hasFile('path_image')) {
            $imagePath = $request->file('path_image')->store('notices', 'public');
        }

        $notice = Notice::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'notice' => $validated['notice'],
            'path_image' => $imagePath,
            'slug' => Str::slug($validated['title']),
        ]);

        return response()->json([
            'message' => 'Notícia criada com sucesso.',
            'data' => $notice->load('category')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $notice = Notice::with('category')
            ->whereHas('category', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->findOrFail($id);

        return response()->json([
            'data' => $notice
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $notice = Notice::with('category')->findOrFail($id);

        abort_if($notice->category->user_id !== auth()->id(), 403, 'Acesso negado.');

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'notice' => ['required', 'string'],
            'path_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        abort_if($category->user_id !== auth()->id(), 403, 'Acesso negado.');

        if ($request->hasFile('path_image')) {
            if ($notice->path_image && Storage::disk('public')->exists($notice->path_image)) {
                Storage::disk('public')->delete($notice->path_image);
            }

            $notice->path_image = $request->file('path_image')->store('notices', 'public');
        }

        $notice->category_id = $validated['category_id'];
        $notice->title = $validated['title'];
        $notice->description = $validated['description'];
        $notice->notice = $validated['notice'];
        $notice->slug = Str::slug($validated['title']);

        $notice->save();

        return response()->json([
            'message' => 'Notícia atualizada com sucesso.',
            'data' => $notice->load('category')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notice = Notice::with('category')->findOrFail($id);

        abort_if($notice->category->user_id !== auth()->id(), 403, 'Acesso negado.');

        if ($notice->path_image && Storage::disk('public')->exists($notice->path_image)) {
            Storage::disk('public')->delete($notice->path_image);
        }

        $notice->delete();

        return response()->json([
            'message' => 'Notícia removida com sucesso.'
        ], 200);
    }


}
