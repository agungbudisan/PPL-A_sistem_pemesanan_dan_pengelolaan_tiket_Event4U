<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
