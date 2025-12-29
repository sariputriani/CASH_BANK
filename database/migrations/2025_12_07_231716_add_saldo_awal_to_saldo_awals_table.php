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
    Schema::table('saldo_awals', function (Blueprint $table) {
        $table->bigInteger('saldo_awal')->after('id_rekening')->nullable();
    });
}

public function down()
{
    Schema::table('saldo_awals', function (Blueprint $table) {
        $table->dropColumn('saldo_awal');
    });
}

};
