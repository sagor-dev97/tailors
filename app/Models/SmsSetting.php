<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
   use HasFactory;

    protected $fillable = [
        'provider',
        'api_key',
        'sender_id',
        'api_url',
        'service_status',
        'admission_status'
    ];

    protected $casts = [
        'service_status' => 'boolean',
        'admission_status' => 'boolean'
    ];
}
