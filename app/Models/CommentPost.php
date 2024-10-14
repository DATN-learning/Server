<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentPost extends Model
{
    use HasFactory;
    protected $table = 'comment_posts';
    protected $primaryKey = 'comment_id';
    protected $fillable = [
        'comment_id',
        'user_id',
        'post_id',
        'title',
        'body',
        'approved',
        'spam',
        'trash',
        'notify',
    ];


    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
