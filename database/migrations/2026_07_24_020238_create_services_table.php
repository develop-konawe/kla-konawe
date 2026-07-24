<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('contact_info')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('services');
    }
};
