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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->nullable();
            $table->foreignId('section_id')->constrained('sections')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->string('phone_number_2', 20)->nullable();
            $table->string('mobile_number_1', 20)->nullable();
            $table->string('mobile_number_2', 20)->nullable();
            $table->string('fax_number', 20)->nullable();
            $table->string('whatsapp_number', 20)->nullable();
            $table->string('facebook_link', 255)->nullable();
            $table->string('linked_in_link', 255)->nullable();
            $table->string('instagram_link', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->enum('checkByAdmin',['accepted','waiting','rejected'])->default('waiting');
            $table->enum('status',['available','busy'])->default('available');
            $table->string('imageSSN')->nullable();
            $table->string('company_image')->nullable();
            $table->string('livePhoto')->nullable();
            $table->string('nationalId')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
