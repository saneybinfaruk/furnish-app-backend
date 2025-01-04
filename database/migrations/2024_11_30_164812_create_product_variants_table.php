<?php

use App\Models\Product;
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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('color_name')->nullable();
            $table->string('color_value');
            $table->string('color_option');
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();;
            $table->decimal('price');
            $table->string('discount_type');
            $table->string('discount');
            $table->string('stock');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
