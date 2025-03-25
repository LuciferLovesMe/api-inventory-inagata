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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_items');
            $table->unsignedBigInteger('id_users');
            $table->unsignedBigInteger('id_warehouses');
            $table->enum('type', ['in', 'out']);
            $table->date('movement_date');
            $table->timestamps();

            $table->foreign('id_items')
                ->references('id')
                ->on('items')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('id_users')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            
            $table->foreign('id_warehouses')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
