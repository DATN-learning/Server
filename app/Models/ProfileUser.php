<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileUser extends Model
{
    use HasFactory;
    protected $table = 'profile_users';
    protected $fillable = [
        'id_profile',
        'user_id ',
        'nick_name',
        'address',
        'date_of_birth',
        'gender',
        'id_image',
        'id_cover_image',
        'hashtag',
        'level_number',
        'experience_point',
        'number_stars',
        'school_name',
        'class_room_id',
        'created_at',
        'updated_at',
    ];
}
