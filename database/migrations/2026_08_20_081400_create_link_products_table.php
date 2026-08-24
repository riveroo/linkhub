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
        Schema::create('link_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained('link_pages')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('link_categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('image_url')->nullable();
            $table->text('product_url');
            $table->string('status')->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_products');
    }
};
