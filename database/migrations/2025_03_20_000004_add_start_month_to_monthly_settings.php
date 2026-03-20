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
        Schema::table('monthly_settings', function (Blueprint $table) {
            $table->integer('start_month')->default(4)->after('due_day'); // April
            $table->integer('start_year')->default(2025)->after('start_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_settings', function (Blueprint $table) {
            $table->dropColumn(['start_month', 'start_year']);
        });
    }
};
