<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

class AdminKaryaController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Content::with('category')
            ->where('user_id', auth()->id());

        // search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request){
                $q->where('title','like','%'.$request->search.'%');
            });
        }

        // filter kategori
        if ($request->filled('category_id')) {
            $query->where('category_id',$request->category_id);
        }

        $contents = $query->latest()->get();

        return view('admin.karya.index', compact('contents','categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.karya.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $path = $file->store('contents', 'public');
        $fullPath = storage_path('app/public/' . $path);

        $mime = $file->getMimeType();

        $type = str_contains($mime,'image') ? 'image' : (str_contains($mime,'video') ? 'video' : 'audio');

        $width = null;
        $height = null;
        $duration = null;

        // IMAGE
        if (str_contains($mime, 'image')) {

            [$width, $height] = getimagesize($fullPath);

        }

        // VIDEO
        if (str_contains($mime, 'video')) {

            $ffprobe = FFProbe::create([
                'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
                'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
            ]);

            $duration = $ffprobe->format($fullPath)->get('duration');

            $videoStream = $ffprobe
                ->streams($fullPath)
                ->videos()
                ->first();

            if ($videoStream) {
                $width = $videoStream->get('width');
                $height = $videoStream->get('height');
            }
        }

        // AUDIO
        if (str_contains($mime, 'audio')) {

            $ffprobe = FFProbe::create([
                'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
                'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
            ]);

            $duration = $ffprobe->format($fullPath)->get('duration');
        }

        Content::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'user_id' => auth()->id(),
            'file_path' => $path,
            'width' => $width,
            'height' => $height,
            'duration' => $duration ? round($duration) : null,
            'description' => $request->description,
            'type' => $type,
            'file_size' => $file->getSize(),
            'status' => 'approved'
        ]);

        return redirect()->route('admin.karya.index')
            ->with('success', 'Karya berhasil diupload!');
    }

    public function destroy(Content $content)
    {
        $content->delete();

        return back()->with('success','Karya berhasil dihapus');
    }
}