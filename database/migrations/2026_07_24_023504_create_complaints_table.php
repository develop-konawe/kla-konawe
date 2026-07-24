<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('category_id')->nullable();
            $table->string('reporter_name')->nullable();
            $table->string('reporter_contact')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('victim_relation')->nullable();
            $table->text('chronology');
            $table->string('general_location')->nullable();
            $table->string('urgency')->default('low');
            $table->timestamp('consent_at')->nullable();
            $table->string('status')->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('complaints');
    }
};
