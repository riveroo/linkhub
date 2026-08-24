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
        Schema::create('link_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('store_name');
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('cover_type')->default('color');
            $table->string('cover_color')->default('#4f46e5');
            $table->string('cover_image')->nullable();
            $table->string('header_alignment')->default('center');
            $table->string('product_layout')->default('grid');
            $table->string('whatsapp_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_pages');
    }
};
