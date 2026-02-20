<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Content::with('category','user')
            ->where('status','approved');

        // SEARCH
        if ($request->filled('search')) {
            $query->where('title','like','%'.$request->search.'%');
        }

        // FILTER KATEGORI
        if ($request->filled('category_id')) {
            $query->where('category_id',$request->category_id);
        }

        // FILTER JENIS MEDIA
        if ($request->filled('media_type')) {
            $query->where('file_path','like','%.'.$request->media_type);
        }

        // FILTER MIN WIDTH
        if ($request->filled('min_width')) {
            $query->where('width','>=',$request->min_width);
        }

        // FILTER MIN DURATION
        if ($request->filled('min_duration')) {
            $query->where('duration','>=',$request->min_duration);
        }

        $contents = $query->latest()->paginate(12);

        return view('gallery.index', compact('contents','categories'));
    }

    public function download(Content $content)
    {
        if($content->status != 'approved'){
            abort(403);
        }

        return Storage::disk('public')->download($content->file_path);
    }
}