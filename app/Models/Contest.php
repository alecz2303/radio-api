<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{
    use HasFactory;

    protected $fillable = ['station_id','title','question','option_a','option_b','option_c','correct_option','max_winners','prize','redemption_instructions','starts_at','ends_at','is_active','close_when_full'];
    protected $casts = ['starts_at'=>'datetime','ends_at'=>'datetime','is_active'=>'boolean','close_when_full'=>'boolean','max_winners'=>'integer'];

    public function station() { return $this->belongsTo(Station::class); }
    public function entries() { return $this->hasMany(ContestEntry::class); }

    public function scopeCurrentlyActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at','<=',$now))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at','>=',$now));
    }
}
