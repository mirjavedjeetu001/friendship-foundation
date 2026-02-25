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
        Schema::create('monthly_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('monthly_contribution_amount', 10, 2)->default(500);
            $table->integer('due_day')->default(10); // day of month when payment is due
            $table->decimal('bank_balance', 12, 2)->default(0);
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_settings');
    }
};
