<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageTemplate extends Model
{
    protected $primaryKey = 'template_id';

    protected $fillable = [
        'name',
        'description',
        'canvas_width',
        'canvas_height',
        'background_type',
        'background_path',
        'audio_path',
        'layout_json',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by','user_id');
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class,'template_id','template_id');
    }

}
