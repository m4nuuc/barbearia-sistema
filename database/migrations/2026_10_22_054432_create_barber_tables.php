<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela de Serviços (Corte, Barba, Sobrancelha, etc.)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration_minutes');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });

        // 2. Tabela de Agendamentos dos Clientes
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_phone');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('services');
    }
};
