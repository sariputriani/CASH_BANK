<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bank_keluars', function (Blueprint $table) {
            $table->unsignedBigInteger('dokumen_id')->nullable()->change();
            $table->string('no_agenda')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('bank_keluars', function (Blueprint $table) {
            $table->unsignedBigInteger('dokumen_id')->nullable(false)->change();
            $table->string('no_agenda')->nullable(false)->change();
        });
    }
};
