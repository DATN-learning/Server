<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';
    protected $fillable = [
        'id_answer',
        'question_id',
        'answer_text',
        'slug',
        'created_at',
        'updated_at',
    ];
}
