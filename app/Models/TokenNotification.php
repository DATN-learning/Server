<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenNotification extends Model
{
    use HasFactory;
    protected $table = 'token_notifications';
    protected $fillable = [
        'id_token_notification',
        'user_id',
        'type',
        'token',
        'device'
    ];
}
