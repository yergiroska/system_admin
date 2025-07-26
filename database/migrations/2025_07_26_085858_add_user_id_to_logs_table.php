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
        Schema::table('logs', function (Blueprint $table) {
            // Añadir la columna user_id como unsignedBigInteger
            $table->unsignedBigInteger('user_id')->after('id');

            // Crear la foreign key constraint
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // Elimina los logs si se borra el usuario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            // Eliminar la foreign key primero
            $table->dropForeign(['user_id']);

            // Eliminar la columna
            $table->dropColumn('user_id');
        });
    }
};
