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
        Schema::table('simulations', function (Blueprint $table) {

            $table->boolean('is_template')
                ->default(false)
                ->after('status');

            $table->unsignedBigInteger('source_simulation_id')
                ->nullable()
                ->after('is_template');

            $table->foreign('source_simulation_id')
                ->references('simulation_id')
                ->on('simulations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulations', function (Blueprint $table) {

            $table->dropForeign([
                'source_simulation_id'
            ]);

            $table->dropColumn([
                'is_template',
                'source_simulation_id'
            ]);
        });
    }
};