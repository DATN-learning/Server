<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LesstionChapter extends Model
{
    use HasFactory;
    protected $table = 'lesstion_chapters';
    protected $primaryKey = 'id_lesstion_chapter';
    protected $fillable = [
        'id_lesstion_chapter',
        'chapter_subject_id ',
        'name_lesstion_chapter',
        'description_lesstion_chapter',
        'number_lesstion_chapter',
        'created_at',
        'updated_at',
    ];
    public function chapterSubject()
    {
        return $this->belongsTo(ChapterSubject::class, 'chapter_subject_id', 'id');
    }
}
