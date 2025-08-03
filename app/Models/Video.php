<?php

// app/Models/Video.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'camera_id', 'path', 'start_time', 'end_time',
        'trigger_type', 'alert_id'
    ];

    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }
}
