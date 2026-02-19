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

}
