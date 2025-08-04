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
        Schema::create('company_product', function (Blueprint $table) {
           // $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('product_id');

            // Claves foráneas
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Clave primaria compuesta (opcional, pero recomendado para evitar duplicados)
            $table->primary(['company_id', 'product_id']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_product');
    }
};
