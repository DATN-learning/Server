<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';

    protected $fillable = [
        'id_post',
        'user_id',
        'title',
        'description',
        'class_room_id',
        'subject_id',
        'category_post',
        'created_at',
        'updated_at',
    ];

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id', 'id');
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function getComments()
    {
        // sort by created_at ascending
        return $this->hasMany(CommentPost::class, 'post_id', 'id')->orderBy('created_at', 'desc');
    }
    public function getDataAnalytics()
    {
        return $this->hasMany(PostAnalysisData::class, 'post_id', 'id');
    }
}
