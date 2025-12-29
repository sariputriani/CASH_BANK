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
            $table->unsignedBigInteger('id_sub_kriteria')->nullable()->after('id_kategori_kriteria');
            $table->foreign('id_sub_kriteria')
            ->references('id_sub_kriteria')
            ->on('sub_kriteria')
            ->nullOnDelete();
            $table->unsignedBigInteger('id_item_sub_kriteria')->nullable()->after('id_sub_kriteria');
            $table->foreign('id_item_sub_kriteria')
            ->references('id_item_sub_kriteria')
            ->on('item_sub_kriteria')
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
