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
        Schema::create('link_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('link_categories')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['category_id', 'name']);
        });

        Schema::table('link_products', function (Blueprint $table) {
            $table->foreignId('subcategory_id')
                ->nullable()
                ->after('category_id')
                ->constrained('link_subcategories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('link_products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subcategory_id');
        });

        Schema::dropIfExists('link_subcategories');
    }
};

