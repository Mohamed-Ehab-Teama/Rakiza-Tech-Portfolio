<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Websites launched for clients — the "Websites" section on the home page.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('client')->nullable();
            $table->string('category')->nullable();          // e.g. "E-commerce", "Corporate"
            $table->text('description')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->string('status', 20)->default('live');   // live | in_progress | archived
            $table->date('launched_on')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
