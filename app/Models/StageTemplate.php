<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageTemplate extends Model
{
    protected $primaryKey = 'template_id';

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by','user_id');
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class,'template_id','template_id');
    }

}
