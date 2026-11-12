<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The "Stack" section: categories (Frontend, Backend…) and their skills.
 * A skill can appear as a tag in its category card, as a proficiency bar
 * (when it has a level), and in one of the two scrolling marquee rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('meta', 40)->nullable();          // small label, e.g. "UI / UX"
            $table->string('icon', 20)->default('other');    // frontend|backend|database|devops|design|mobile|cloud|other
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('level')->nullable();   // 0–100; shown as a bar when set
            $table->boolean('show_in_card')->default(true);     // tag inside the category card
            $table->unsignedTinyInteger('marquee_row')->default(1); // 0 = hidden, 1 = top row, 2 = bottom row
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
    }
};
