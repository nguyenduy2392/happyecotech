<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('h1')->nullable();
            $table->json('benefits')->nullable();
            $table->json('steps')->nullable();
            $table->json('faq')->nullable();
        });
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->text('message')->nullable()->change();
            $table->string('source', 30)->default('contact-form');
        });
    }

    public function down(): void
    {
        DB::table('contact_submissions')->whereNull('message')->update(['message' => '']);
        Schema::table('contact_submissions', function (Blueprint $table) {
            $table->text('message')->nullable(false)->change();
            $table->dropColumn('source');
        });
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn(['h1', 'benefits', 'steps', 'faq']));
    }
};
