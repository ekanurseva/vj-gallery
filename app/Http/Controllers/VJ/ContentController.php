<?php

namespace App\Http\Controllers\VJ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('vj.contents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'category_id' => 'required',
            'file' => 'required|file|max:51200'
        ]);

        $file = $request->file('file');

        $path = $file->store('contents', 'public');

        $type = $file->getClientMimeType();

        $contentType = str_contains($type, 'image') ? 'image' :
                    (str_contains($type, 'video') ? 'video' : 'audio');

        Content::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $contentType,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'status' => 'pending'
        ]);

        return redirect()
            ->route('vj.contents.create')
            ->with('success','Konten berhasil diupload dan menunggu persetujuan');
    }

}
