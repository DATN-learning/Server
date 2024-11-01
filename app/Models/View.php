<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;
    protected $table = 'views';
    protected $fillable = [
        'view_id',
        'user_id',
        'id_view_query',
        'start_time',
        'end_time',
    ];

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
