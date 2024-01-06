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
        Schema::create ('choices', function (Blueprint $table) {
            $table->bigInteger('id', 20)->unsigned()->nullable(false);
            $table->string('choice', 191)->nullable(false);
            $table->bigInteger('poll_id')->unsigned()->nullable(false);
            $table->timestamps();
            
            $table->foreign('poll_id')->references('id')->on('polls')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('choices');
    }
};