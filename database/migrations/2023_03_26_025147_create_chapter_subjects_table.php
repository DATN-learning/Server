<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chapter_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('id_chapter_subject');
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('name_chapter_subject');
            $table->integer('number_chapter');
            $table->string('chapter_image');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_subjects');
    }
};
