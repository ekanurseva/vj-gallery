<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StageTemplate;
use App\Models\Simulation;
use App\Models\SimulationContent;

class SimulationController extends Controller
{
    public function create($template_id)
    {
        $template = StageTemplate::findOrFail($template_id);

        return view('simulations.create', compact('template'));
    }

    public function store(Request $request, $template_id)
    {
        $template = StageTemplate::findOrFail($template_id);

        $simulation = Simulation::create([
            'template_id'   => $template_id,
            'user_id'       => auth()->id(),
            'title'         => $request->title,
            'description'   => $request->description,
            'layout_json'   => $template->layout_json,
            'canvas_width'  => $template->canvas_width,
            'canvas_height' => $template->canvas_height,
            'status'        => 'draft'
        ]);

        return redirect()->route('simulations.builder',$simulation->simulation_id);
    }

    public function builder($simulation_id)
    {
        $simulation = Simulation::with('contents.content')
            ->findOrFail($simulation_id);

        $layout = json_decode($simulation->layout_json, true) ?? [];

        $visualContents = $simulation->contents
            ->filter(fn($item) =>
                in_array($item->content->type ?? null, ['image','video'])
            )
            ->sortByDesc('layer_order');

        $audioContents = $simulation->contents
            ->filter(fn($item) =>
                ($item->content->type ?? null) === 'audio'
            );

        return view('simulations.builder', compact(
            'simulation',
            'layout',
            'visualContents',
            'audioContents'
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

        $visuals = $request->visuals ?? [];
        $audios = $request->audios ?? [];

        // Hapus Konten Lama
        SimulationContent::where('simulation_id', $simulation_id)->delete();

        // SAVE VISUAL
        foreach($visuals as $v){

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
                'duration'      => $v['duration'],
            ]);
        }

        // SAVE AUDIO
        foreach($audios as $a){

            SimulationContent::create([
                'simulation_id' => $simulation_id,
                'content_id'    => $a['content_id'],
                'slot_id'       => null,
                'pos_x'         => 0,
                'pos_y'         => 0,
                'width'         => 0,
                'height'        => 0,
                'layer_order'   => 0,
                'start_time' => $a['start_time'],
                'duration'   => $a['duration'],
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
