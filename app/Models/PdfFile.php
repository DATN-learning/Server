<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfFile extends Model
{
    use HasFactory;
    protected $table = 'pdf_files';
    protected $fillable = [
        'id_pdf',
        'id_query_pdf',
        'url_pdf',
        'slug',
        'created_at',
        'updated_at',
    ];
}
