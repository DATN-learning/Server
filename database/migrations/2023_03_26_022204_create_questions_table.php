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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('id_question');
            $table->string('id_question_query');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('answer_correct');
            $table->set('level_question', ['Easy', 'Medium', 'Hard'])->default('Easy');
            $table->text('number_question')->nullable();
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
