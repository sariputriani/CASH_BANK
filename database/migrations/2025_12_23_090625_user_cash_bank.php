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
         Schema::create('users_cash_bank', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->enum('role', ['admin','vendor'])->default('admin');

            // TANPA after()
            $table->unsignedBigInteger('id_bank_tujuan')->nullable();

            $table->foreign('id_bank_tujuan')
                ->references('id_bank_tujuan')
                ->on('bank_tujuan')
                ->nullOnDelete();

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
