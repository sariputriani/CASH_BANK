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
         DB::statement("UPDATE bank_keluars SET kredit = '0' WHERE kredit IS NULL OR kredit = '' OR kredit NOT REGEXP '^[0-9]+(\\.[0-9]+)?$'");
        Schema::table('bank_keluars',function(Blueprint $table){
            $table->decimal('debet', 38,2)->default(0)->change();
            $table->decimal('kredit', 38,2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_keluars', function (Blueprint $table) {
            $table->string('kredit', 255)->nullable()->change();
        });
    }
};
