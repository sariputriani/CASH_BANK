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
       Schema::create('item_sub_kriteria', function (Blueprint $table) {
            $table->id('id_item_sub_kriteria');
            $table->foreignId('id_sub_kriteria')
                ->constrained('sub_kriteria', 'id_sub_kriteria')
                ->onDelete('cascade');
            $table->string('nama_item_sub_kriteria');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_sub_kriteria_tabel');
    }
};
