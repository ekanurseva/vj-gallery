<?php

namespace App\Http\Controllers\Vj;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Category;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

class VjContentController extends Controller
{
    public function index()
    {
        $contents = Content::where('user_id', auth()->id())
                    ->latest()
                    ->get();

        return view('vj.contents.index', compact('contents'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('vj.contents.create', compact('categories'));
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
            'status' => 'pending'
        ]);

        return redirect()->route('vj.contents.index')
            ->with('success','Konten berhasil diupload dan menunggu persetujuan admin');
    }

    public function destroy(Content $content)
    {
        if($content->user_id != auth()->id()){
            abort(403);
        }

        $content->delete();

        return back()->with('success','Konten berhasil dihapus');
    }
}