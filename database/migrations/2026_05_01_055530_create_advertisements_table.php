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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image');
            $table->text('link');
            $table->enum('position', ['header', 'sidebar', 'footer', 'inline', 'popup'])->default('popup');
            $table->enum('status', ['active', 'inactive']);
            $table->date('start_at');
            $table->date('end_at');
            $table->unsignedInteger('view_count');
            $table->unsignedInteger('click_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
