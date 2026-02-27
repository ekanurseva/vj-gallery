<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StageTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StageTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = StageTemplate::latest()->get();

        return view('admin.stage_templates.index', [
            'templates' => $templates,
            'isAdmin' => true
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.stage_templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'canvas_width' => 'required|integer|min:100',
            'canvas_height' => 'required|integer|min:100',
            'background_type' => 'required|in:color,video,image',
            'background_file' => 'nullable|file|mimes:jpg,jpeg,png,mp4',
            'audio_file' => 'nullable|file|mimes:mp3,wav',
        ]);

        $backgroundPath = null;
        $audioPath = null;

        if ($request->hasFile('background_file')) {
            $backgroundPath = $request->file('background_file')
                ->store('stage_templates/backgrounds', 'public');
        }

        if ($request->hasFile('audio_file')) {
            $audioPath = $request->file('audio_file')
                ->store('stage_templates/audios', 'public');
        }

        $template = StageTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'canvas_width' => $request->canvas_width,
            'canvas_height' => $request->canvas_height,
            'background_type' => $request->background_type,
            'background_path' => $backgroundPath,
            'audio_path' => $audioPath,
            'created_by' => auth()->user()->user_id,
        ]);

        return redirect()
            ->route('admin.stage_templates.builder', $template)
            ->with('success', 'Template dibuat. Silakan atur layout.');
    }

    /**
     * Display the specified resource.
     */
    public function show(StageTemplate $stage_template)
    {
        return view('admin.stage_templates.show', compact('stage_template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StageTemplate $stage_template)
    {
        return view('admin.stage_templates.edit', compact('stage_template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StageTemplate $stage_template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'canvas_width' => 'required|integer|min:100',
            'canvas_height' => 'required|integer|min:100',
            'background_type' => 'required|in:color,video,image',
            'background_file' => 'nullable|file|mimes:jpg,jpeg,png,mp4',
            'audio_file' => 'nullable|file|mimes:mp3,wav',
        ]);

        // Replace background
        if ($request->hasFile('background_file')) {
            if ($stage_template->background_path) {
                Storage::disk('public')->delete($stage_template->background_path);
            }

            $stage_template->background_path = $request->file('background_file')
                ->store('stage_templates/backgrounds', 'public');
        }

        // Replace audio
        if ($request->hasFile('audio_file')) {
            if ($stage_template->audio_path) {
                Storage::disk('public')->delete($stage_template->audio_path);
            }

            $stage_template->audio_path = $request->file('audio_file')
                ->store('stage_templates/audios', 'public');
        }

        // Replace layout json
        if ($request->hasFile('layout_json')) {
            if ($stage_template->layout_json_path) {
                Storage::disk('public')->delete($stage_template->layout_json_path);
            }

            $stage_template->layout_json_path = $request->file('layout_json')
                ->store('stage_templates/layouts', 'public');
        }

        $stage_template->update([
            'name' => $request->name,
            'description' => $request->description,
            'canvas_width' => $request->canvas_width,
            'canvas_height' => $request->canvas_height,
            'background_type' => $request->background_type,
        ]);

        return redirect()
            ->route('admin.stage_templates.index')
            ->with('success', 'Template panggung berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StageTemplate $stage_template)
    {
        if ($stage_template->background_path) {
            Storage::disk('public')->delete($stage_template->background_path);
        }

        if ($stage_template->audio_path) {
            Storage::disk('public')->delete($stage_template->audio_path);
        }

        if ($stage_template->layout_json_path) {
            Storage::disk('public')->delete($stage_template->layout_json_path);
        }

        $stage_template->delete();

        return redirect()
            ->route('admin.stage_templates.index')
            ->with('success', 'Template panggung berhasil dihapus.');
    }

    public function builder(StageTemplate $stage_template)
    {
        return view('admin.stage_templates.builder', compact('stage_template'));
    }

    public function saveLayout(Request $request, StageTemplate $stage_template)
    {
        $stage_template->update([
            'layout_json' => json_encode($request->layout)
        ]);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function publicIndex()
    {
        $templates = StageTemplate::latest()->get();

        return view('admin.stage_templates.index', [
            'templates' => $templates,
            'isAdmin' => false
        ]);
    }
}