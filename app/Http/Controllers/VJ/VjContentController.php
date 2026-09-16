<?php

namespace App\Http\Controllers\Vj;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Content;
use App\Models\Category;
use App\Models\Theme;
use FFMpeg\FFProbe;
use Throwable;

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

        $themes = Theme::orderBy('name')->get();

        return view(
            'vj.contents.create',
            compact('categories', 'themes')
        );
    }


    /**
     * Menyimpan konten VJ
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi Input Dasar
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'title' =>
                'required|string|max:255',

            'category_id' =>
                'required|exists:categories,category_id',

            'file' =>
                'required|file|max:51200|mimes:
                jpg,jpeg,png,gif,webp,
                mp4,webm,mov,
                mp3,wav,ogg',

            'description' =>
                'nullable|string',

            'theme_ids' =>
                'nullable|array',

            'theme_ids.*' =>
                'exists:themes,theme_id',

        ], [

            'title.required' =>
                'Judul karya wajib diisi.',

            'title.max' =>
                'Judul karya maksimal 255 karakter.',

            'category_id.required' =>
                'Kategori karya wajib dipilih.',

            'category_id.exists' =>
                'Kategori yang dipilih tidak valid.',

            'file.required' =>
                'File karya wajib dipilih.',

            'file.max' =>
                'Ukuran file terlalu besar. Maksimal 50 MB.',

            'file.mimes' =>
                'Format file tidak didukung. Gunakan JPG, PNG, GIF, WEBP, MP4, WEBM, MOV, MP3, WAV, atau OGG.',

            'theme_ids.*.exists' =>
                'Tema yang dipilih tidak valid.',

        ]);


        /*
        |--------------------------------------------------------------------------
        | Ambil File
        |--------------------------------------------------------------------------
        */

        $file = $request->file('file');

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

            $imageSize = @getimagesize(
                $file->getRealPath()
            );

            if (!$imageSize) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'file' =>
                            'File gambar tidak dapat dibaca atau rusak.'
                    ]);

            }

            $width = $imageSize[0];
            $height = $imageSize[1];


            if (
                $width > 3840 ||
                $height > 2160
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'file' =>
                            'Dimensi gambar terlalu besar. Maksimal 3840 x 2160 piksel.'
                    ]);

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

            try {

                $ffprobe = FFProbe::create([

                    'ffmpeg.binaries' =>
                        'C:/ffmpeg/bin/ffmpeg.exe',

                    'ffprobe.binaries' =>
                        'C:/ffmpeg/bin/ffprobe.exe',

                ]);


                $duration = $ffprobe
                    ->format($file->getRealPath())
                    ->get('duration');


                if (
                    $duration === null ||
                    !is_numeric($duration)
                ) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'file' =>
                                'Durasi file tidak dapat dibaca. Pastikan file tidak rusak.'
                        ]);

                }


                if ($duration > 60) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'file' =>
                                'Durasi karya terlalu panjang. Maksimal 60 detik.'
                        ]);

                }


                if ($type === 'video') {

                    $videoStream = $ffprobe
                        ->streams($file->getRealPath())
                        ->videos()
                        ->first();


                    if (!$videoStream) {

                        return back()
                            ->withInput()
                            ->withErrors([
                                'file' =>
                                    'Video tidak dapat dibaca atau tidak memiliki stream video yang valid.'
                            ]);

                    }


                    $width = $videoStream->get('width');
                    $height = $videoStream->get('height');


                    if (
                        !$width ||
                        !$height
                    ) {

                        return back()
                            ->withInput()
                            ->withErrors([
                                'file' =>
                                    'Dimensi video tidak dapat dibaca.'
                            ]);

                    }

                }


            } catch (Throwable $e) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'file' =>
                            'File video/audio tidak dapat diproses. Pastikan file tidak rusak dan formatnya sesuai.'
                    ]);

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Simpan File
        |--------------------------------------------------------------------------
        */

        try {

            $path = $file->store(
                'contents',
                'public'
            );

        } catch (Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'File gagal disimpan. Silakan coba kembali.'
                ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Simpan Content
        |--------------------------------------------------------------------------
        */

        try {

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
                    $duration !== null
                        ? round($duration)
                        : null,

                'description' =>
                    $request->description,

                'type' =>
                    $type,

                'file_size' =>
                    $file->getSize(),

                // Karya VJ menunggu persetujuan Admin
                'status' =>
                    'pending',

            ]);


            /*
            |--------------------------------------------------------------------------
            | Simpan Relasi Tema
            |--------------------------------------------------------------------------
            */

            $content->themes()->sync(
                $request->input('theme_ids', [])
            );


        } catch (Throwable $e) {

            Storage::disk('public')->delete($path);

            return back()
                ->withInput()
                ->withErrors([
                    'file' =>
                        'Terjadi kesalahan saat menyimpan karya. Silakan coba kembali.'
                ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Berhasil
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