<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;
    protected $table = 'class_rooms';
    protected $fillable = [
        'id_class_room',
        'name_class',
        'class',
        'slug',
        'created_at',
        'updated_at',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_room_id', 'id');
    }
}