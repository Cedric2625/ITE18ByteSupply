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
        Schema::create('hardware_components', function (Blueprint $table) {
            $table->id();
            $table->string('component_reference_number')->unique();
            $table->string('component_name');
            $table->string('brand');
            $table->string('model');
            $table->text('specifications');
            $table->decimal('retail_price', 10, 2);
            $table->decimal('seller_price', 10, 2);
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->timestamp('date_created');
            $table->timestamp('date_order')->nullable();
            $table->timestamp('date_arrive')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hardware_components');
    }
};
