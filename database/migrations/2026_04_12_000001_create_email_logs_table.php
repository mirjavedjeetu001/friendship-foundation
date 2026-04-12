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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('type'); // payment_reminder, registration_approved, etc.
            $table->string('subject');
            $table->text('message_preview')->nullable();
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->text('error_message')->nullable();
            $table->string('month_year')->nullable(); // For payment reminders: "April 2026"
            $table->decimal('amount', 12, 2)->nullable(); // Amount mentioned in email
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete(); // If manually sent
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'status']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
