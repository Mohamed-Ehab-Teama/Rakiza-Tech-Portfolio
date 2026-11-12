<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Numbers shown on the site.
 *   group "highlight" → the stats under the hero and in the About section
 *   group "dashboard" → the KPI cards in the "By the numbers" dashboard
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->string('group', 20)->default('highlight');
            $table->string('label');                       // "Years experience" / "Deploys"
            $table->string('short_label')->nullable();     // "years" (hero) — falls back to label
            $table->decimal('value', 14, 2)->default(0);
            $table->unsignedTinyInteger('decimals')->default(0);
            $table->string('prefix', 10)->nullable();      // e.g. "$"
            $table->string('suffix', 10)->nullable();      // e.g. "+" or "%"
            $table->string('badge', 20)->nullable();       // dashboard card corner tag, e.g. "YTD"
            $table->string('note', 120)->nullable();       // dashboard card footer, e.g. "+12.4% this month"
            $table->string('trend', 10)->default('up');    // up | down | flat
            $table->string('color', 10)->default('accent'); // accent | term | violet | amber
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['group', 'is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
