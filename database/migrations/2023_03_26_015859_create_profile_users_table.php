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
        Schema::create('profile_users', function (Blueprint $table) {
            $table->id();
            $table->string('id_profile');
            $table->unsignedBigInteger('user_id');
            $table->string('nick_name')->nullable();
            $table->string('address')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->set('gender', ['Male', 'Female'])->default('Male');
            $table->string('id_image');
            $table->string('id_cover_image');
            $table->string('hashtag');
            $table->integer('level_number')->default(1);
            $table->integer('experience_point')->default(0);
            $table->integer('number_stars')->default(0);
            $table->string('school_name')->nullable();
            $table->string('class_name')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_users');
    }
};
