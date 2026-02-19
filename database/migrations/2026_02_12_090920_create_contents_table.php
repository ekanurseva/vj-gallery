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
        Schema::create('contents', function (Blueprint $table) {
            $table->id('content_id');

            $table->foreignId('user_id')
                ->constrained('users','user_id')
                ->onDelete('cascade');

            $table->foreignId('category_id')
                ->constrained('categories','category_id')
                ->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type',['image','video','audio']);
            $table->string('file_path');

            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('duration')->nullable();
            $table->bigInteger('file_size')->nullable();

            $table->enum('status',['approved','rejected'])->default('approved');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
