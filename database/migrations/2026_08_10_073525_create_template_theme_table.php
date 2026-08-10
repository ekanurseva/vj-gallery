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
        Schema::create('template_theme', function (Blueprint $table) {

            $table->id('template_theme_id');

            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('theme_id');

            $table->foreign('template_id')
                ->references('template_id')
                ->on('stage_templates')
                ->cascadeOnDelete();

            $table->foreign('theme_id')
                ->references('theme_id')
                ->on('themes')
                ->cascadeOnDelete();

            $table->unique(['template_id', 'theme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_theme');
    }
};