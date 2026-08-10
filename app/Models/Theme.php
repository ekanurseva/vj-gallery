<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $primaryKey = 'theme_id';

    protected $fillable = [
        'name',
        'color'
    ];

    public function templates()
    {
        return $this->belongsToMany(
            StageTemplate::class,
            'template_theme',
            'theme_id',
            'template_id',
            'theme_id',
            'template_id'
        );
    }

    public function contents()
    {
        return $this->belongsToMany(
            Content::class,
            'content_theme',
            'theme_id',
            'content_id',
            'theme_id',
            'content_id'
        );
    }
}