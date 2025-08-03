<?php

// app/Models/Face.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Face extends Model
{
    protected $fillable = ['name', 'tag', 'photo_path', 'encodings'];

    protected $casts = [
        'encodings' => 'array',
    ];

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
