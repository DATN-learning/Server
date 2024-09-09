<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = [
        'id_question',
        'id_question_query',
        'title',
        'description',
        'answer_correct',
        'level_question',
        'number_question',
        'slug',
        'created_at',
        'updated_at',
    ];
    public function Answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }
}
