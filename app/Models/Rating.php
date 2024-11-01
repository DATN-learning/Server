<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $table = 'ratings';
    protected $fillable = [
        'rating_id',
        'user_id',
        'lesstion_chapter_id',
        'content',
        'rating',
    ];

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function lesstionChapter()
    {
        return $this->belongsTo(LesstionChapter::class, 'lesstion_chapter_id', 'id');
    }
}
