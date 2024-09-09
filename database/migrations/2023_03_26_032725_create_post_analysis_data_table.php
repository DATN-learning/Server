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
        Schema::create('post_analysis_data', function (Blueprint $table) {
            $table->id();
            $table->string('id_post_analysis_data');
            $table->unsignedBigInteger('post_id');
            $table->text('text_data');
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_analysis_data');
    }
};
