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
        Schema::create ('votes', function (Blueprint $table) {
            $table->bigInteger('id', 20)->unsigned()->nullable(false);
            $table->bigInteger('choice_id')->unsigned()->nullable(false);
            $table->bigInteger('user_id')->unsigned()->nullable(false);
            $table->bigInteger('poll_id')->unsigned()->nullable(false);
            $table->bigInteger('division_id')->unsigned()->nullable(false);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('division_id')->references('division_id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('poll_id')->references('id')->on('polls')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('choice_id')->references('id')->on('choices')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};