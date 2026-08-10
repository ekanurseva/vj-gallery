<?php

namespace App\Http\Controllers\Vj;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Content;
use App\Models\Category;
use App\Models\Theme;
use FFMpeg\FFProbe;

class VjContentController extends Controller
{
    /**
     * Menampilkan karya milik VJ
     */
    public function index(Request $request)
    {
        $categories = Category::all();
        $themes = Theme::all();

        $query = Content::with(['category', 'themes'])
            ->where('user_id', Auth::id());

        // SEARCH
        if ($request->filled('search')) {
            $query->where(
                'title',
                'like',
                '%' . $request->search . '%'
            );
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

        return view(
            'vj.contents.index',
            compact(
                'contents',
                'categories',
                'themes'
            )
        );
    }


    /**
     * Menampilkan form upload konten
     */
    public function create()
    {
        $categories = Category::all();
        $themes = Theme::all();

        return view(
            'vj.contents.create',
            compact(
                'categories',
                'themes'
            )
        );
    }


    /**
     * Menyimpan konten VJ
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',

            'category_id' =>
                'required|exists:categories,category_id',

            'file' =>
                'required|file',

            'description' =>
                'nullable|string',

            'theme_ids' =>
                'nullable|array',

            'theme_ids.*' =>
                'exists:themes,theme_id',
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
        | Tentukan Tipe Konten
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
                    'file' =>
                        'Format file tidak didukung.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Metadata
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

            $imageSize =
                getimagesize($fullPath);

            if ($imageSize) {

                $width = $imageSize[0];
                $height = $imageSize[1];

            }
        }


        /*
        |--------------------------------------------------------------------------
        | VIDEO / AUDIO
        |--------------------------------------------------------------------------
        */

        if (
            $type === 'video' ||
            $type === 'audio'
        ) {

            $ffprobe = FFProbe::create([
                'ffmpeg.binaries' =>
                    'C:/ffmpeg/bin/ffmpeg.exe',

                'ffprobe.binaries' =>
                    'C:/ffmpeg/bin/ffprobe.exe',
            ]);


            // DURASI
            $duration = $ffprobe
                ->format($fullPath)
                ->get('duration');


            // DIMENSI VIDEO
            if ($type === 'video') {

                $videoStream = $ffprobe
                    ->streams($fullPath)
                    ->videos()
                    ->first();

                if ($videoStream) {

                    $width =
                        $videoStream->get('width');

                    $height =
                        $videoStream->get('height');
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Simpan Content
        |--------------------------------------------------------------------------
        */

        $content = Content::create([

            'title' =>
                $request->title,

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

            /*
             * Konten VJ harus menunggu
             * persetujuan Admin.
             */
            'status' =>
                'pending',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Simpan Relasi Tema
        |--------------------------------------------------------------------------
        */

        $content->themes()->sync(
            $request->input(
                'theme_ids',
                []
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('vj.contents.index')
            ->with(
                'success',
                'Konten berhasil diupload dan menunggu persetujuan admin'
            );
    }


    /**
     * Menghapus konten milik VJ
     */
    public function destroy(Content $content)
    {
        if (
            $content->user_id !== Auth::id()
        ) {
            abort(403);
        }

        $content->delete();

        return back()->with(
            'success',
            'Konten berhasil dihapus'
        );
    }
}