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
        Schema::table('simulations', function (Blueprint $table) {

            // kalau ada foreign key → drop dulu
            $table->dropForeign(['theme_id']);

            // baru drop kolom
            $table->dropColumn('theme_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('simulations', function (Blueprint $table) {

            $table->foreignId('theme_id')->nullable()
                ->constrained('themes','theme_id')
                ->nullOnDelete();
        });
    }
};
