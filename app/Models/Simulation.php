<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    protected $primaryKey = 'simulation_id';

    public function template()
    {
        return $this->belongsTo(StageTemplate::class,'template_id','template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }

    public function simulationContents()
    {
        return $this->hasMany(SimulationContent::class,'simulation_id','simulation_id');
    }
}
