<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Camera extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'device_id', 'rtsp_url', 'stream_url', 'is_active', 'is_online'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
    ];

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
