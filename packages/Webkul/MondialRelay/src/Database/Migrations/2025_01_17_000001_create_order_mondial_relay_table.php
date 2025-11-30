<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_mondial_relay', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id');
            $table->string('delivery_mode', 10); // 24R (Point Relais & Locker), HOM (Domicile)
            $table->string('point_relais_id', 20)->nullable();
            $table->string('point_relais_name')->nullable();
            $table->text('point_relais_address')->nullable();
            $table->string('point_relais_city', 100)->nullable();
            $table->string('point_relais_postcode', 10)->nullable();
            $table->string('point_relais_country', 2)->nullable();
            $table->string('tracking_number', 50)->nullable();
            $table->string('label_url')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_mondial_relay');
    }
};
