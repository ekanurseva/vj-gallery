<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    protected $primaryKey = 'simulation_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'template_id',
        'user_id',
        'title',
        'description',
        'layout_json',
        'canvas_width',
        'canvas_height',
        'is_public',
        'status',
        'is_template',
        'source_simulation_id'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_template' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(StageTemplate::class,'template_id');
    }

    public function sourceSimulation()
    {
        return $this->belongsTo(
            Simulation::class,
            'source_simulation_id',
            'simulation_id'
        );
    }

    public function clonedSimulations()
    {
        return $this->hasMany(
            Simulation::class,
            'source_simulation_id',
            'simulation_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }

    public function contents()
    {
        return $this->hasMany(
            SimulationContent::class,
            'simulation_id',
            'simulation_id'
        );
    }

    public function simulationContents()
    {
        return $this->hasMany(SimulationContent::class, 'simulation_id', 'simulation_id');
    }
}
