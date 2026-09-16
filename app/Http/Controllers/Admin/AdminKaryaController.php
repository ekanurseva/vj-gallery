<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\Category;
use App\Models\Theme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use FFMpeg\FFProbe;
use Throwable;
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
        /*
        |--------------------------------------------------------------------------
        | Validasi Input Dasar
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'title' => 'required|string|max:255',

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
        | Metadata Awal
        |--------------------------------------------------------------------------
        */

        $width = null;
        $height = null;
        $duration = null;


        /*
        |--------------------------------------------------------------------------
        | Validasi IMAGE
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


            // Batas dimensi gambar
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
        | Validasi VIDEO / AUDIO
        |--------------------------------------------------------------------------
        */

        if (
            $type === 'video' ||
            $type === 'audio'
        ) {

            try {
                $ffprobe = FFProbe::create([
                    'ffmpeg.binaries'  => 'C:/ffmpeg/bin/ffmpeg.exe',
                    'ffprobe.binaries' => 'C:/ffmpeg/bin/ffprobe.exe',
                    'timeout'          => 60,
                ]);

                $duration = $ffprobe
                    ->format($file->getRealPath())
                    ->get('duration');

                if ($duration === null) {
                    throw ValidationException::withMessages([
                        'file' => 'Durasi video tidak dapat dibaca. Pastikan file video tidak rusak.',
                    ]);
                }

                $duration = (int) round((float) $duration);

                if ($duration > 300) {
                    throw ValidationException::withMessages([
                        'file' => 'Durasi video terlalu panjang. Maksimal durasi video adalah 5 menit.',
                    ]);
                }
            } catch (ValidationException $e) {
                return back()
                    ->withInput()
                    ->withErrors($e->errors());
            } catch (Throwable $e) {
                report($e);

                return back()
                    ->withInput()
                    ->withErrors([
                        'file' => 'Video gagal diproses. Pastikan format dan ukuran video sesuai, lalu coba lagi.',
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
        | Simpan Data Content
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

                // Karya Admin langsung disetujui
                'status' =>
                    'approved',

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

            // Hapus file jika database gagal menyimpan
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