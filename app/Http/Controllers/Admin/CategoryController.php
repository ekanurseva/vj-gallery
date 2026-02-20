<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Store kategori baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_type' => 'required|in:image,video,audio,all'
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'target_type' => $request->target_type,
        ]);

        return redirect()
            ->route('admin.contents.index')
            ->with('success','Kategori berhasil ditambahkan');
    }

    /**
     * Update kategori
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'target_type' => 'required|in:image,video,audio,all'
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'target_type' => $request->target_type,
        ]);

        return redirect()
            ->route('admin.contents.index')
            ->with('success','Kategori berhasil diperbarui');
    }

    /**
     * Hapus kategori
     */
    public function destroy(Category $category)
    {
        if ($category->is_system) {
            return back()->with('error','Kategori sistem tidak bisa dihapus');
        }

        $category->delete();

        return back()->with('success','Kategori berhasil dihapus');
    }
}
