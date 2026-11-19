<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('category_label', 80)->nullable();
            $table->string('tagline', 70)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn(['category_label', 'tagline']));
    }
};
