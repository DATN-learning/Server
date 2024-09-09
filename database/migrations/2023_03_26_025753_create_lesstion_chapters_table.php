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
        Schema::create('lesstion_chapters', function (Blueprint $table) {
            $table->id();
            $table->string('id_lesstion_chapter');
            $table->foreignId('chapter_subject_id')->constrained()->cascadeOnDelete();
            $table->string('name_lesstion_chapter');
            $table->string('description_lesstion_chapter');
            $table->integer('number_lesstion_chapter');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesstion_chapters');
    }
};
