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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date');
            $table->string('purpose');
            $table->string('spent_by'); // Name of person who made the expense
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->string('receipt')->nullable(); // Receipt/voucher image
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // Approval details
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Fund source - where the money comes from
            $table->enum('fund_source', ['monthly_savings', 'manual'])->nullable();
            $table->text('fund_source_note')->nullable(); // Description for manual adjustment
            
            // Who created this expense entry
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
