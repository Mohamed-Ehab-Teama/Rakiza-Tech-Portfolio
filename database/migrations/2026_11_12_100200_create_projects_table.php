<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Portfolio projects — the "Selected work" section and the /projects pages.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();            // used in the URL: /projects/{slug}
            $table->string('role')->nullable();          // one-line subtitle, e.g. "Parking management platform"
            $table->string('client')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->text('summary')->nullable();         // short intro (cards, meta description)
            $table->longText('description')->nullable(); // full write-up (Markdown) on the project page
            $table->json('tags')->nullable();            // tech stack, e.g. ["Laravel", "MySQL"]
            $table->string('visual', 20)->default('dashboard'); // card artwork: dashboard|store|kanban|terminal|map|image
            $table->string('cover_path')->nullable();    // uploaded screenshot (storage/app/public/…)
            $table->string('live_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 300)->nullable();
            $table->boolean('is_featured')->default(true);    // shown on the home page
            $table->boolean('is_published')->default(false);  // visible to visitors at all
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
