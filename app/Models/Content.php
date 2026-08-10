<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $primaryKey = 'content_id';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'type',
        'file_path',
        'width',
        'height',
        'duration',
        'file_size',
        'status'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','category_id');
    }

    public function getDurationFormattedAttribute()
    {
        if (!$this->duration) return null;

        return gmdate("H:i:s", $this->duration);
    }

    public function themes()
    {
        return $this->belongsToMany(
            Theme::class,
            'content_theme',
            'content_id',
            'theme_id',
            'content_id',
            'theme_id'
        );
    }
}
