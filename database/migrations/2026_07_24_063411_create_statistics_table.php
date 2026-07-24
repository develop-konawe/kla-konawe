<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('category_id')->nullable();
            $table->string('title');
            $table->integer('year');
            $table->string('region_id')->nullable();
            $table->decimal('value', 15, 2);
            $table->string('unit')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('draft');
            $table->json('metadata_json')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void {
        Schema::dropIfExists('statistics');
    }
};
