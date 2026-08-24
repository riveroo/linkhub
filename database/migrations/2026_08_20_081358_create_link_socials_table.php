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
        Schema::create('link_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_page_id')->constrained('link_pages')->cascadeOnDelete();
            $table->string('platform');
            $table->text('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_socials');
    }
};
