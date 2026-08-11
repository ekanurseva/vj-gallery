<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StageTemplate;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StageTemplateController extends Controller
{
    /**
     * Menampilkan daftar template panggung
     */
    public function index()
    {
        $templates = StageTemplate::with('themes')
            ->latest()
            ->get();

        return view('admin.stage_templates.index', [
            'templates' => $templates,
            'isAdmin' => true
        ]);
    }


    /**
     * Form tambah template
     */
    public function create()
    {
        $themes = Theme::all();

        return view(
            'admin.stage_templates.create',
            compact('themes')
        );
    }


    /**
     * Menyimpan template baru
     */
    public function store(Request $request)
    {
        $request->validate([

            'name' =>
                'required|string|max:255',

            'description' =>
                'nullable|string',

            'canvas_width' =>
                'required|integer|min:100',

            'canvas_height' =>
                'required|integer|min:100',

            'background_type' =>
                'required|in:color,video,image',

            'background_file' =>
                'nullable|file|mimes:jpg,jpeg,png,mp4',

            'audio_file' =>
                'nullable|file|mimes:mp3,wav',

            'theme_ids' =>
                'nullable|array',

            'theme_ids.*' =>
                'exists:themes,theme_id',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Upload Background
        |--------------------------------------------------------------------------
        */

        $backgroundPath = null;

        if ($request->hasFile('background_file')) {

            $backgroundPath =
                $request->file('background_file')
                    ->store(
                        'stage_templates/backgrounds',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Upload Audio
        |--------------------------------------------------------------------------
        */

        $audioPath = null;

        if ($request->hasFile('audio_file')) {

            $audioPath =
                $request->file('audio_file')
                    ->store(
                        'stage_templates/audios',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Create Template
        |--------------------------------------------------------------------------
        */

        $template = StageTemplate::create([

            'name' =>
                $request->name,

            'description' =>
                $request->description,

            'canvas_width' =>
                $request->canvas_width,

            'canvas_height' =>
                $request->canvas_height,

            'background_type' =>
                $request->background_type,

            'background_path' =>
                $backgroundPath,

            'audio_path' =>
                $audioPath,

            'created_by' =>
                Auth::id(),
        ]);


        /*
        |--------------------------------------------------------------------------
        | Simpan Tema Template
        |--------------------------------------------------------------------------
        */

        $template->themes()->sync(
            $request->input('theme_ids', [])
        );


        return redirect()
            ->route(
                'admin.stage_templates.builder',
                $template
            )
            ->with(
                'success',
                'Template dibuat. Silakan atur layout.'
            );
    }


    /**
     * Menampilkan detail template
     */
    public function show(StageTemplate $stage_template)
    {
        $stage_template->load('themes');

        return view(
            'admin.stage_templates.show',
            compact('stage_template')
        );
    }


    /**
     * Form edit template
     */
    public function edit(StageTemplate $stage_template)
    {
        $themes = Theme::all();

        $stage_template->load('themes');

        return view(
            'admin.stage_templates.edit',
            compact(
                'stage_template',
                'themes'
            )
        );
    }


    /**
     * Update template
     */
    public function update(
        Request $request,
        StageTemplate $stage_template
    ) {
        $request->validate([

            'name' =>
                'required|string|max:255',

            'description' =>
                'nullable|string',

            'canvas_width' =>
                'required|integer|min:100',

            'canvas_height' =>
                'required|integer|min:100',

            'background_type' =>
                'required|in:color,video,image',

            'background_file' =>
                'nullable|file|mimes:jpg,jpeg,png,mp4',

            'audio_file' =>
                'nullable|file|mimes:mp3,wav',

            'theme_ids' =>
                'nullable|array',

            'theme_ids.*' =>
                'exists:themes,theme_id',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Replace Background
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('background_file')) {

            if ($stage_template->background_path) {

                Storage::disk('public')
                    ->delete(
                        $stage_template->background_path
                    );
            }

            $stage_template->background_path =
                $request->file('background_file')
                    ->store(
                        'stage_templates/backgrounds',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Replace Audio
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('audio_file')) {

            if ($stage_template->audio_path) {

                Storage::disk('public')
                    ->delete(
                        $stage_template->audio_path
                    );
            }

            $stage_template->audio_path =
                $request->file('audio_file')
                    ->store(
                        'stage_templates/audios',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | Update Data Template
        |--------------------------------------------------------------------------
        */

        $stage_template->update([

            'name' =>
                $request->name,

            'description' =>
                $request->description,

            'canvas_width' =>
                $request->canvas_width,

            'canvas_height' =>
                $request->canvas_height,

            'background_type' =>
                $request->background_type,

            'background_path' =>
                $stage_template->background_path,

            'audio_path' =>
                $stage_template->audio_path,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update Tema Template
        |--------------------------------------------------------------------------
        */

        $stage_template->themes()->sync(
            $request->input('theme_ids', [])
        );


        return redirect()
            ->route(
                'admin.stage_templates.index'
            )
            ->with(
                'success',
                'Template panggung berhasil diperbarui.'
            );
    }


    /**
     * Hapus template
     */
    public function destroy(
        StageTemplate $stage_template
    ) {
        if ($stage_template->background_path) {

            Storage::disk('public')
                ->delete(
                    $stage_template->background_path
                );
        }


        if ($stage_template->audio_path) {

            Storage::disk('public')
                ->delete(
                    $stage_template->audio_path
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Hapus Template
        |--------------------------------------------------------------------------
        |
        | Relasi template_theme akan ikut terhapus
        | karena foreign key menggunakan cascadeOnDelete().
        |
        */

        $stage_template->delete();


        return redirect()
            ->route(
                'admin.stage_templates.index'
            )
            ->with(
                'success',
                'Template panggung berhasil dihapus.'
            );
    }


    /**
     * Builder template
     */
    public function builder(
        StageTemplate $stage_template
    ) {
        $stage_template->load('themes');

        return view(
            'admin.stage_templates.builder',
            compact('stage_template')
        );
    }


    /**
     * Simpan layout template
     */
    public function saveLayout(
        Request $request,
        StageTemplate $stage_template
    ) {
        $stage_template->update([

            'layout_json' =>
                json_encode($request->layout)

        ]);

        return response()->json([
            'status' => 'success'
        ]);
    }


    /**
     * Tampilan template untuk VJ
     */
    public function publicIndex()
    {
        $templates = StageTemplate::with('themes')
            ->latest()
            ->get();

        return view(
            'admin.stage_templates.index',
            [
                'templates' => $templates,
                'isAdmin' => false
            ]
        );
    }
}