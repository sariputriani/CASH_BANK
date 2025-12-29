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
        Schema::table('bank_masuk', function(Blueprint $table){
            $table->unsignedBigInteger('id_sumber_dana')->nullable()->change();
            $table->unsignedBigInteger('id_bank_tujuan')->nullable()->change();

            $table->unsignedBigInteger('id_kategori_kriteria')->nullable()->change();
            $table->unsignedBigInteger('id_sub_kriteria')->nullable()->change();
            $table->unsignedBigInteger('id_item_sub_kriteria')->nullable()->change();

            $table->string('uraian')->nullable()->change();
            $table->decimal('nilai_rupiah', 38,2)->default(0)->change();
            $table->decimal('debet', 38,2)->default(0)->change();
            $table->decimal('kredit', 38,2)->default(0)->change();
            // $table->string('penerima')->nullable();
            $table->date('tanggal')->nullable()->change();
            // $table->string('keterangan')->nullable();
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
