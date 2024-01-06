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
        Schema::create ('users', function (Blueprint $table) {
            $table->bigInteger('id', 20)->unsigned()->nullable(false);
            $table->string('username', 191)->nullable(false);
            $table->string('password', 191)->nullable(false);
            $table->string('role', 191);
            $table->bigInteger('division_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('division_id')->references('id')->on('divisions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};