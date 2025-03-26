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
        Schema::table('total_items', function (Blueprint $table) {
            $table->unsignedBigInteger('id_warehouses')->after('id_items');

            $table->foreign('id_warehouses')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('total_items', function (Blueprint $table) {
            //
        });
    }
};
