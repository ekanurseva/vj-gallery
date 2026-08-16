<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Theme;
use App\Models\Content;

class ContentManagementController extends Controller
{
    public function index(Request $request)
    {   
        $categories = Category::all();

        $query = Content::with('user','category')->latest();

        $themes = Theme::orderBy('name')->get();

        // Search Judul
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter Status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $contents = $query->get();

        return view('admin.contents.index', compact('categories','contents', 'themes'));
    }

    public function storeTheme(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:themes,name',
            'description' => 'nullable|string',
        ]);

        Theme::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('admin.contents.index')
            ->with('success', 'Tema berhasil ditambahkan.');
    }

    public function updateTheme(Request $request, Theme $theme)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:themes,name,' . $theme->theme_id . ',theme_id',
            'description' => 'nullable|string',
        ]);

        $theme->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('admin.contents.index')
            ->with('success', 'Tema berhasil diperbarui.');
    }

    public function destroyTheme(Theme $theme)
    {
        $theme->delete();

        return redirect()
            ->route('admin.contents.index')
            ->with('success', 'Tema berhasil dihapus.');
    }

    public function approve(Content $content)
    {
        $content->update(['status' => 'approved']);

        return back()->with('success','Konten berhasil disetujui');
    }

    public function reject(Content $content)
    {
        $content->update(['status' => 'rejected']);

        return back()->with('success','Konten berhasil ditolak');
    }

    public function destroy(Content $content)
    {
        $content->delete();

        return back()->with('success','Konten berhasil dihapus');
    }
}