<?php

// app/Models/Alert.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'camera_id', 'snapshot_path', 'confidence', 'type',
        'is_recognized', 'face_id'
    ];

    protected $casts = [
        'is_recognized' => 'boolean',
        'confidence' => 'float',
    ];

    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    public function face()
    {
        return $this->belongsTo(Face::class);
    }
}
