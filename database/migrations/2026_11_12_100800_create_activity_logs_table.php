<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Who changed what in the dashboard (Dashboard → Activity).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 30);                 // created | updated | deleted | signed_in | …
            $table->nullableMorphs('subject');            // subject_type + subject_id of the changed record
            $table->string('description');                // human sentence, e.g. 'Updated project "LMS"'
            $table->json('properties')->nullable();       // extra details, e.g. which fields changed
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
