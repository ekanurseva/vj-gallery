<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationContent extends Model
{
    protected $primaryKey = 'sim_content_id';
    public $timestamps = false;

    protected $fillable = [
        'simulation_id',
        'content_id',
        'layer_order',
        'start_time',
        'duration',
        'pos_x',
        'pos_y',
        'width',
        'height',
        'opacity',
        'rotation',
        'scale',
        'slot_id'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class,'content_id','content_id');
    }

    public function simulation()
    {
        return $this->belongsTo(Simulation::class,'simulation_id','simulation_id');
    }

}
