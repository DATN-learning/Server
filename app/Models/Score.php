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
        'question_query_id',
        'score',
    ];

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
