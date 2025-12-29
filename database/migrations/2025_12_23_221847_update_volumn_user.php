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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin','vendor'])->default('admin');

            // TANPA after()
            $table->unsignedBigInteger('id_bank_tujuan')->nullable()->after('remember_token');

            $table->foreign('id_bank_tujuan')
                ->references('id_bank_tujuan')
                ->on('bank_tujuan')
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
