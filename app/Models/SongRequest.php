<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongRequest extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_SEEN = 'seen';
    public const STATUS_ATTENDED = 'attended';
    public const STATUS_DISCARDED = 'discarded';

    protected $fillable = [
        'station_id',
        'channel_id',
        'listener_name',
        'song',
        'artist',
        'dedication',
        'status',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
