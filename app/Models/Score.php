<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id_score',
        'user_id',
        'question_id',
        'answer_id',
        'is_correct',
        'score',
    ];

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Questions()
    {
        return $this->hasMany(Question::class, 'question_id', 'id');
    }

    public function answers()
    {
        return $this->belongsTo(Answer::class, 'answer_id', 'id');
    }
}
