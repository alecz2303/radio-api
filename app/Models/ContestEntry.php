<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestEntry extends Model
{
    use HasFactory;

    protected $fillable = ['contest_id','device_key','push_token','listener_name','phone','selected_option','is_correct','is_winner','claim_code','claimed_at','winner_notified_at'];
    protected $casts = ['is_correct'=>'boolean','is_winner'=>'boolean','claimed_at'=>'datetime','winner_notified_at'=>'datetime'];

    public function contest() { return $this->belongsTo(Contest::class); }
}
