<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'category_id';

    public function contents()
    {
        return $this->hasMany(Content::class,'category_id','category_id');
    }

}
