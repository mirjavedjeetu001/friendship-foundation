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
        Schema::table('contributions', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_approved_by')->nullable()->after('approved_at');
            $table->timestamp('admin_approved_at')->nullable()->after('admin_approved_by');
            $table->unsignedBigInteger('accountant_approved_by')->nullable()->after('admin_approved_at');
            $table->timestamp('accountant_approved_at')->nullable()->after('accountant_approved_by');

            $table->foreign('admin_approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('accountant_approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropForeign(['admin_approved_by']);
            $table->dropForeign(['accountant_approved_by']);
            $table->dropColumn(['admin_approved_by', 'admin_approved_at', 'accountant_approved_by', 'accountant_approved_at']);
        });
    }
};
