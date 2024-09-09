<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterSubject extends Model
{
    use HasFactory;
    protected $table = 'chapter_subjects';
    protected $fillable = [
        'id_chapter_subject',
        'subject_id',
        'name_chapter_subject',
        'chapter_image',
        'slug',
        'created_at',
        'updated_at',
        'number_chapter',
    ];

    public function subjects()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function lessions()
    {
        return $this->hasMany(LesstionChapter::class, 'chapter_subject_id', 'id');
    }
}
