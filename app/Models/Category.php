<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon', // Sekarang berisi path file gambar
        'description'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
