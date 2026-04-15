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
        Schema::table('expenses', function (Blueprint $table) {
            // Payment type: cash (needs bank settlement) or bank (direct from bank)
            $table->enum('payment_type', ['cash', 'bank'])->default('cash')->after('fund_source_note');
            
            // Bank settlement status for cash expenses
            $table->enum('settlement_status', ['pending', 'settled', 'not_applicable'])->default('not_applicable')->after('payment_type');
            
            // Settlement details
            $table->foreignId('settled_by')->nullable()->after('settlement_status')->constrained('users')->nullOnDelete();
            $table->timestamp('settled_at')->nullable()->after('settled_by');
            $table->text('settlement_note')->nullable()->after('settled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['settled_by']);
            $table->dropColumn(['payment_type', 'settlement_status', 'settled_by', 'settled_at', 'settlement_note']);
        });
    }
};
