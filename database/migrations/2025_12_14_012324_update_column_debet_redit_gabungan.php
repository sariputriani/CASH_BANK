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
        Schema::table('gabungan_masuk_keluars',function(Blueprint $table){
            $table->decimal('debet', 38,2)->default(0)->change();
            $table->decimal('kredit', 38,2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gabungan_masuk_keluars', function (Blueprint $table) {
            $table->decimal('debet', 15, 2)->change();
            $table->decimal('kredit', 15, 2)->change();
        });
    }
};
