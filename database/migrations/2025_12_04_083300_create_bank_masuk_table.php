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
        Schema::create('bank_masuk', function (Blueprint $table) {
            $table->id('id_bank_masuk');

            $table->string('agenda_tahun')->nullable(); 

            $table->unsignedBigInteger('id_sumber_dana');
            $table->unsignedBigInteger('id_bank_tujuan')->nullable();

            $table->unsignedBigInteger('id_kategori_kriteria');
            $table->unsignedBigInteger('id_sub_kriteria');
            $table->unsignedBigInteger('id_item_sub_kriteria');

            $table->string('uraian');
            $table->bigInteger('nilai_rupiah');
            $table->string('penerima')->nullable();
            $table->date('tanggal_masuk');
            $table->string('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_masuk');
    }
};
