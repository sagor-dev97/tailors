<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'created_by',
        'image',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'image',
        'status'
    ];

   public function questions()
   {
    return $this->hasMany(Question::class);
   }

}
