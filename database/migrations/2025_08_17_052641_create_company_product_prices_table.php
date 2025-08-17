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
        Schema::create('company_product_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_product_id');
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('company_product_id')
                ->references('id')->on('company_product')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_product_prices');
    }
};
