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
        Schema::create('member_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('full_name_bangla')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('occupation')->nullable();
            $table->string('designation')->nullable();
            $table->string('organization')->nullable();
            
            // Contact Information
            $table->string('phone_secondary')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            
            // Address
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            
            // National ID
            $table->string('nid_number')->nullable();
            $table->string('nid_front_photo')->nullable();
            $table->string('nid_back_photo')->nullable();
            
            // Passport Photo
            $table->string('passport_photo')->nullable();
            
            // Nominee Information
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->string('nominee_phone')->nullable();
            $table->string('nominee_nid_number')->nullable();
            $table->string('nominee_photo')->nullable();
            $table->string('nominee_nid_front_photo')->nullable();
            $table->string('nominee_nid_back_photo')->nullable();
            $table->text('nominee_address')->nullable();
            
            // Banking Information
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_routing_number')->nullable();
            $table->enum('account_type', ['savings', 'current'])->nullable();
            $table->string('mobile_banking_provider')->nullable();
            $table->string('mobile_banking_number')->nullable();
            
            // Additional Documents
            $table->string('signature_photo')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_profiles');
    }
};
