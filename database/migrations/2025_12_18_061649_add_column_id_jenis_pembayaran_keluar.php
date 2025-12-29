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
        Schema::table('bank_keluars',function(Blueprint $table){
            $table->unsignedBigInteger('id_jenis_pembayaran')->nullable()->after('penerima');
            $table->foreign('id_jenis_pembayaran')
            ->references('id_jenis_pembayaran')
            ->on('jenis_pembayarans')
            ->nullOnDelete();
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
