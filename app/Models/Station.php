<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'logo_url', 'is_active', 'metadata'];
    protected $casts = ['is_active' => 'boolean', 'metadata' => 'array'];

    public function channels()
    {
        return $this->hasMany(Channel::class)->orderBy('order');
    }
}
