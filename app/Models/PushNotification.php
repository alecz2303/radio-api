<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'title',
        'body',
        'target',
        'action',
        'data',
        'status',
        'recipients_count',
        'success_count',
        'failure_count',
        'error_text',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
    ];
}
