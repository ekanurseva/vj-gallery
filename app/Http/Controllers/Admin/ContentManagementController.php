<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Content;

class ContentManagementController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $contents = Content::with('user','category')->latest()->get();

        return view('admin.contents.index', compact('categories','contents'));
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