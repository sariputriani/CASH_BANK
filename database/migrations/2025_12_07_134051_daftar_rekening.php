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
        Schema::create('daftarRekenings', function (Blueprint $table){
            $table->id('id_rekening');
            $table->foreignId('id_daftar_bank')
                ->constrained('daftarBanks', 'id_daftar_bank')
                ->onDelete('cascade');
            $table->String('nomor_rekening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
