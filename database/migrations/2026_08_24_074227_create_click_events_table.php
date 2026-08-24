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
        Schema::create('click_events', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('link_id')->constrained('links')->cascadeOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('sources')->nullOnDelete();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('link_products')->nullOnDelete();

            // Event classification
            $table->string('event_type'); // page_view | product_click

            // Simple privacy-friendly visitor identifier (generated from session, no IP stored)
            $table->string('visitor_id', 64)->nullable();

            // Primary timestamp — used for time-based analytics queries
            $table->timestamp('clicked_at')->useCurrent();

            $table->timestamps();

            // Analytics indexes for fast aggregation queries
            $table->index(['link_id', 'clicked_at']);
            $table->index(['source_id', 'clicked_at']);
            $table->index(['product_id', 'clicked_at']);
            $table->index(['event_type', 'clicked_at']);
            $table->index(['link_id', 'event_type', 'clicked_at']); // composite for dashboard queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('click_events');
    }
};
