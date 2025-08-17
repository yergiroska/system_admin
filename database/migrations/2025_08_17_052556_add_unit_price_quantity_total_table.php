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
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->after('company_product_id');
            // Si manejas cantidad/total:
            $table->unsignedInteger('quantity')->default(1)->after('unit_price');
            $table->decimal('total', 12, 2)->nullable()->after('quantity');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'quantity', 'total']);

        });
    }
};
