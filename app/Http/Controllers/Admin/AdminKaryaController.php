<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Category;
use App\Models\Theme;
use Illuminate\Support\Facades\Auth;
use FFMpeg\FFProbe;

class AdminKaryaController extends Controller
{
    /**
     * Menampilkan daftar karya milik Admin
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $themes = Theme::all();

        $query = Content::with(['category', 'themes'])
            ->where('user_id', Auth::id());

        // SEARCH JUDUL
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    'title',
                    'like',
                    '%' . $request->search . '%'
                );
            });
        }

        // FILTER KATEGORI
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        $contents = $query
            ->latest()
            ->get();

        return view('admin.karya.index', compact(
            'contents',
            'categories',
            'themes'
        ));
    }


    /**
     * Menampilkan halaman tambah karya
     */
    public function create()
    {
        $categories = Category::all();
        $themes = Theme::all();

        return view(
            'admin.karya.create',
            compact('categories', 'themes')
        );
    }


    /**
     * Menyimpan karya baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'file' => 'required|file',
            'description' => 'nullable|string',
            'theme_ids' => 'nullable|array',
            'theme_ids.*' => 'exists:themes,theme_id',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Upload File
        |--------------------------------------------------------------------------
        */

        $file = $request->file('file');

        $path = $file->store(
            'contents',
            'public'
        );

        $fullPath = storage_path(
            'app/public/' . $path
        );

        $mime = $file->getMimeType();


        /*
        |--------------------------------------------------------------------------
        | Menentukan Tipe Konten
        |--------------------------------------------------------------------------
        */

        if (str_contains($mime, 'image')) {

            $type = 'image';

        } elseif (str_contains($mime, 'video')) {

            $type = 'video';

        } elseif (str_contains($mime, 'audio')) {

            $type = 'audio';

        } else {

            return back()
                ->withInput()
                ->withErrors([
                    'file' => 'Format file tidak didukung.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Metadata Konten
        |--------------------------------------------------------------------------
        */

        $width = null;
        $height = null;
        $duration = null;


        /*
        |--------------------------------------------------------------------------
        | IMAGE
        |--------------------------------------------------------------------------
        */

        if ($type === 'image') {

            $imageSize = getimagesize($fullPath);

            if ($imageSize) {

                $width = $imageSize[0];
                $height = $imageSize[1];

            }
        }


        /*
        |--------------------------------------------------------------------------
        | FFPROBE
        |--------------------------------------------------------------------------
        */

        if ($type === 'video' || $type === 'audio') {

            $ffprobe = FFProbe::create([
                'ffmpeg.binaries' =>
                    'C:/ffmpeg/bin/ffmpeg.exe',

                'ffprobe.binaries' =>
                    'C:/ffmpeg/bin/ffprobe.exe',
            ]);


            /*
            |--------------------------------------------------------------------------
            | Duration
            |--------------------------------------------------------------------------
            */

            $duration = $ffprobe
                ->format($fullPath)
                ->get('duration');


            /*
            |--------------------------------------------------------------------------
            | VIDEO DIMENSION
            |--------------------------------------------------------------------------
            */

            if ($type === 'video') {

                $videoStream = $ffprobe
                    ->streams($fullPath)
                    ->videos()
                    ->first();

                if ($videoStream) {

                    $width = $videoStream->get('width');
                    $height = $videoStream->get('height');

                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan Content
        |--------------------------------------------------------------------------
        */

        $content = Content::create([
            'title' => $request->title,

            'category_id' =>
                $request->category_id,

            'user_id' =>
                Auth::id(),

            'file_path' =>
                $path,

            'width' =>
                $width,

            'height' =>
                $height,

            'duration' =>
                $duration
                    ? round($duration)
                    : null,

            'description' =>
                $request->description,

            'type' =>
                $type,

            'file_size' =>
                $file->getSize(),

            // Konten Admin langsung disetujui
            'status' =>
                'approved',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Simpan Tema Konten
        |--------------------------------------------------------------------------
        |
        | theme_ids berasal dari checkbox tema pada form.
        |
        */

        $content->themes()->sync(
            $request->input('theme_ids', [])
        );


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('admin.karya.index')
            ->with(
                'success',
                'Karya berhasil diupload!'
            );
    }


    /**
     * Menghapus karya
     */
    public function destroy(Content $content)
    {
        // Pastikan karya yang dihapus adalah milik Admin
        if ($content->user_id !== Auth::id()) {
            abort(403);
        }

        $content->delete();

        return back()
            ->with(
                'success',
                'Karya berhasil dihapus'
            );
    }
}