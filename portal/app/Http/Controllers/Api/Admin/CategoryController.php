<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())->latest()->get();

        return response()->json([
            'data' => $categories
        ],200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        $category = Category::create([
            'user_id' => auth()->id(),
            'name'    => $validated['name'],
            'slug'    => Str::slug($validated['name']),
        ]);

        return response()->json([
            'message' => 'Categoria criada com sucesso.',
            'data' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        abort_if($category->user_id !== auth()->id(), 403, 'Acesso negado.');

        return response()->json([
            'data' => $category
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        abort_if($category->user_id !== auth()->id(), 403, 'Acesso negado.');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return response()->json([
            'message' => 'Categoria atualizada com sucesso.',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        abort_if($category->user_id !== auth()->id(), 403, 'Acesso negado.');

        $category->delete();

        return response()->json([
            'message' => 'Categoria removida com sucesso.'
        ], 200);
    }
}
