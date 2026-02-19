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
        Schema::create('simulation_contents', function (Blueprint $table) {
            $table->id('sim_content_id');

            $table->foreignId('simulation_id')
                ->constrained('simulations','simulation_id')
                ->onDelete('cascade');

            $table->foreignId('content_id')
                ->constrained('contents','content_id')
                ->onDelete('cascade');

            $table->integer('layer_order');
            $table->integer('start_time')->nullable();
            $table->integer('duration')->nullable();

            $table->integer('pos_x')->nullable();
            $table->integer('pos_y')->nullable();

            $table->integer('width')->nullable();
            $table->integer('height')->nullable();

            $table->float('opacity')->default(1);
            $table->float('rotation')->default(0);
            $table->float('scale')->default(1);

            $table->timestamp('created_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_contents');
    }
};
