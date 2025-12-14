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
        Schema::create('products', function (Blueprint $table) {
           $table->id();
        $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('pinterest_url')->nullable(); // The design source
        $table->string('status')->default('Idea'); // Idea, Slicing, Printing, Done
        $table->text('remarks')->nullable(); // Slicing settings, filament type, etc.
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
