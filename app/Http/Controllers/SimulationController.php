<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StageTemplate;
use App\Models\Simulation;
use App\Models\SimulationContent;
use App\Models\Content;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SimulationController extends Controller
{
    public function index()
    {
        $simulations = Simulation::where('user_id',  Auth::id())
            ->with(['simulationContents.content'])
            ->latest()
            ->get();

        return view('simulations.index', compact('simulations'));
    }
    
    public function templates(Request $request)
    {
        /* Template Panggung */

        $query = StageTemplate::with('themes')
            ->latest();

        /* Search Template Panggung */

        if ($request->filled('search')) {

            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        /* Filter Tema Template Panggung */

        if ($request->filled('theme_id')) {

            $query->whereHas('themes', function ($q) use ($request) {

                $q->where(
                    'themes.theme_id',
                    $request->theme_id
                );

            });
        }

        $templates = $query->get();

        /* Template Simulation */

        $simulationQuery = Simulation::with([
            'template',
            'template.themes',
            'simulationContents.content'
        ])
            ->where('is_template', true)
            ->where('status', 'published')
            ->latest();

        /* Search Template Simulation */

        if ($request->filled('search')) {

            $simulationQuery->where(
                'title',
                'like',
                '%' . $request->search . '%'
            );
        }

        /* Filter Tema Template Simulation */

        if ($request->filled('theme_id')) {

            $simulationQuery->whereHas(
                'template.themes',
                function ($q) use ($request) {

                    $q->where(
                        'themes.theme_id',
                        $request->theme_id
                    );

                }
            );
        }

        $simulationTemplates = $simulationQuery->get();

        /* Daftar Tema */

        $themes = \App\Models\Theme::orderBy('name')
            ->get();


        return view(
            'vj.templates.index',
            compact(
                'templates',
                'simulationTemplates',
                'themes'
            )
        );
    }

    public function useSimulationTemplate($simulation_id)
    {
        $sourceSimulation = Simulation::with([
            'simulationContents'
        ])->findOrFail($simulation_id);

        // Hanya Simulation yang sudah dijadikan template
        // yang boleh digunakan
        if (!$sourceSimulation->is_template) {
            abort(404);
        }

        // Template Simulation harus sudah dipublikasikan
        if ($sourceSimulation->status !== 'published') {
            abort(404);
        }

        // Buat Simulation baru milik user yang sedang login
        $newSimulation = Simulation::create([
            'template_id' => $sourceSimulation->template_id,
            'user_id' => Auth::id(),
            'title' => $sourceSimulation->title . ' - Copy',
            'description' => $sourceSimulation->description,

            'layout_json' => $sourceSimulation->layout_json,

            'canvas_width' => $sourceSimulation->canvas_width,
            'canvas_height' => $sourceSimulation->canvas_height,

            'is_public' => false,
            'status' => 'draft',

            'is_template' => false,
            'source_simulation_id' => $sourceSimulation->simulation_id,
        ]);

        /* Clone Simulation Contents */

        foreach ($sourceSimulation->simulationContents as $sourceContent) {

            $newSimulation->simulationContents()->create([
                'content_id' => $sourceContent->content_id,

                'layer_order' => $sourceContent->layer_order,
                'start_time' => $sourceContent->start_time,
                'duration' => $sourceContent->duration,

                'pos_x' => $sourceContent->pos_x,
                'pos_y' => $sourceContent->pos_y,

                'width' => $sourceContent->width,
                'height' => $sourceContent->height,

                'opacity' => $sourceContent->opacity,
                'rotation' => $sourceContent->rotation,
                'scale' => $sourceContent->scale,

                'slot_id' => $sourceContent->slot_id,
            ]);
        }

        return redirect()
            ->route(
                'simulations.builder',
                $newSimulation->simulation_id
            )
            ->with(
                'success',
                'Template Simulation berhasil digunakan. Silakan sesuaikan simulasi Anda.'
            );
    }

    public function reference($simulation_id)
    {
        $simulation = Simulation::with([
            'template.themes',
            'simulationContents.content'
        ])->findOrFail($simulation_id);

        /* Pastikan Simulation merupakan Template */

        if (!$simulation->is_template) {
            abort(404);
        }

        /* Pastikan Template sudah dipublikasikan */

        if ($simulation->status !== 'published') {
            abort(404);
        }

        /* Layout */

        $layout = json_decode(
            $simulation->layout_json,
            true
        ) ?? [];


        /* Visual Contents */

        $visualContents = $simulation->simulationContents
            ->filter(function ($item) {

                return in_array(
                    $item->content->type ?? null,
                    ['image', 'video']
                );

            })
            ->sortByDesc('layer_order');


        /* Audio Contents */

        $audioContents = $simulation->simulationContents
            ->filter(function ($item) {

                return ($item->content->type ?? null) === 'audio';

            });


        return view(
            'simulations.reference',
            compact(
                'simulation',
                'layout',
                'visualContents',
                'audioContents'
            )
        );
    }

    public function create($template_id)
    {
        $template = StageTemplate::with('themes')
            ->findOrFail($template_id);

        return view(
            'simulations.create',
            compact('template')
        );
    }

    public function store(Request $request, $template_id)
    {
        $template = StageTemplate::with('themes')
            ->findOrFail($template_id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $simulation = Simulation::create([
            'template_id'   => $template->template_id,
            'user_id'       => Auth::id(),
            'title'         => $request->title,
            'description'   => $request->description,
            'layout_json'   => $template->layout_json,
            'canvas_width'  => $template->canvas_width,
            'canvas_height' => $template->canvas_height,
            'status'        => 'draft',
        ]);

        return redirect()
            ->route(
                'simulations.builder',
                $simulation->simulation_id
            )
            ->with(
                'success',
                'Template berhasil digunakan sebagai draft simulasi.'
            );
    }

    public function builder($simulation_id)
    {
        $simulation = Simulation::with([
            'contents.content',
            'template.themes'
        ])->findOrFail($simulation_id);

        if ($simulation->user_id !== Auth::id()) {
            abort(403);
        }

        /* Layout Template */

        $layout = json_decode($simulation->layout_json, true) ?? [];

        /* Tema Template */

        $themeIds = $simulation->template
            ? $simulation->template->themes->pluck('theme_id')
            : collect();

        /* Konten yang sudah digunakan dalam Simulation */

        $visualContents = $simulation->contents
            ->filter(fn ($item) =>
                in_array(
                    $item->content->type ?? null,
                    ['image', 'video']
                )
            )
            ->sortByDesc('layer_order');

        $audioContents = $simulation->contents
            ->filter(fn ($item) =>
                ($item->content->type ?? null) === 'audio'
            );

        /* Semua Konten yang dapat digunakan */

        $availableContents = Content::where(function ($q) {
            $q->where('status', 'approved')
            ->orWhere('user_id', Auth::id());
        })->get();

        /* Konten Rekomendasi Berdasarkan Tema Template */

        $recommendedContents = collect();

        if ($themeIds->isNotEmpty()) {

            $recommendedContents = Content::with([
                    'themes',
                    'category'
                ])
                ->where(function ($q) {
                    $q->where('status', 'approved')
                    ->orWhere('user_id', Auth::id());
                })
                ->whereHas('themes', function ($q) use ($themeIds) {
                    $q->whereIn('themes.theme_id', $themeIds);
                })
                ->latest()
                ->get();
        }

        /* Pisahkan Visual dan Audio */

        $recommendedVisuals = $recommendedContents
            ->filter(fn ($content) =>
                in_array($content->type, ['image', 'video'])
            );

        $recommendedAudios = $recommendedContents
            ->filter(fn ($content) =>
                $content->type === 'audio'
            );

        return view('simulations.builder', compact(
            'simulation',
            'availableContents',
            'layout',
            'visualContents',
            'audioContents',
            'recommendedVisuals',
            'recommendedAudios'
        ));
    }

    public function saveLayout(Request $request, $simulation_id)
    {
        $simulation = Simulation::findOrFail($simulation_id);

        $simulation->update([
            'layout_json' => $request->layout_json
        ]);

        return response()->json(['success' => true]);
    }

    public function saveContents(Request $request, $simulation_id)
    {
        $simulation = Simulation::findOrFail($simulation_id);

        if ($simulation->user_id !== Auth::id()) {
            abort(403);
        }

        $visuals = $request->visuals ?? [];
        $audios = $request->audios ?? [];

        SimulationContent::where(
            'simulation_id',
            $simulation_id
        )->delete();

        // SAVE VISUAL
        foreach ($visuals as $v) {

            SimulationContent::create([
                'simulation_id' => $simulation_id,
                'content_id'    => $v['content_id'],
                'slot_id'       => $v['slot_id'],
                'pos_x'         => $v['pos_x'],
                'pos_y'         => $v['pos_y'],
                'width'         => $v['width'],
                'height'        => $v['height'],
                'layer_order'   => $v['layer_order'],
                'start_time'    => $v['start_time'],
                'duration'     => $v['duration'],
            ]);
        }

        // SAVE AUDIO
        foreach ($audios as $a) {

            SimulationContent::create([
                'simulation_id' => $simulation_id,
                'content_id'    => $a['content_id'],
                'slot_id'       => null,
                'pos_x'         => 0,
                'pos_y'         => 0,
                'width'         => 0,
                'height'        => 0,
                'layer_order'   => 0,
                'start_time'    => $a['start_time'],
                'duration'      => $a['duration'],
            ]);
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function uploadContent(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200'
        ]);

        $file = $request->file('file');

        $path = $file->store('contents', 'public');

        $mime = $file->getMimeType();
        $type = 'image';

        if (str_contains($mime, 'video')) {
            $type = 'video';
        } elseif (str_contains($mime, 'audio')) {
            $type = 'audio';
        }

        $content = Content::create([
            'user_id' => Auth::id(), 
            'category_id' => 1,      
            'title' => $file->getClientOriginalName(),
            'file_path' => $path,
            'type' => $type,
            'file_size' => $file->getSize(),
            'status' => 'pending'
        ]);

        return response()->json([
            'id' => $content->content_id,
            'title' => $content->title,
            'path' => $content->file_path,
            'type' => $content->type
        ]);
    }

    public function makeTemplate($simulation_id)
    {
        $simulation = Simulation::findOrFail($simulation_id);

        // Hanya pemilik simulation yang boleh menjadikannya template
        if ($simulation->user_id !== Auth::id()) {
            abort(403);
        }

        // Hanya admin yang boleh membuat Simulation Template
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $simulation->update([
            'is_template' => true,
            'is_public' => true,
            'status' => 'published',
        ]);

        return back()->with(
            'success',
            'Simulation berhasil dijadikan Template Simulation.'
        );
    }

    public function destroy(Simulation $simulation)
    {
        if ($simulation->user_id !== Auth::id()) {
            abort(403);
        }

        $simulation->delete();

        return redirect()
            ->route('simulations.index')
            ->with('success', 'Simulasi berhasil dihapus');
    }
}
