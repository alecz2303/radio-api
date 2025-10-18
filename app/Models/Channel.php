<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id', 'name', 'slug', 'order', 'stream_url',
        'backup_url', 'is_active', 'metadata'
    ];
    protected $casts = ['is_active' => 'boolean', 'metadata' => 'array'];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
