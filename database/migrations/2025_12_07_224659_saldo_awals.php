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
        Schema::create('saldo_awals',function (Blueprint $table) {
            $table->id('id_saldo');
            $table->foreignId('id_daftar_bank')
                ->constrained('daftarBanks', 'id_daftar_bank')
                ->onDelete('cascade');
            $table->foreignId('id_rekening')
                ->constrained('daftarRekenings', 'id_rekening')
                ->onDelete('cascade'); 
            $table->timestamps();
        
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
