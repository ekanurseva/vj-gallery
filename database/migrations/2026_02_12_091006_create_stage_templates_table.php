<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stage_templates', function (Blueprint $table) {
            $table->id('template_id');

            $table->string('name');
            $table->text('description')->nullable();

            $table->integer('canvas_width');
            $table->integer('canvas_height');

            $table->enum('background_type',['color','video','image']);
            $table->string('background_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->string('layout_json_path')->nullable();

            $table->foreignId('created_by')
                ->constrained('users','user_id')
                ->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stage_templates');
    }
};
