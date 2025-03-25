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
        Schema::create('total_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_items');
            $table->integer('stock');
            $table->timestamps();

            $table->foreign('id_items')
                ->references('id')
                ->on('items')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('total_items');
    }
};
