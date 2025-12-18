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
        Schema::table('products', function (Blueprint $table) {
        $table->decimal('cost', 10, 2)->nullable(); // e.g., 125.50
    });

    // 2. Create Subtasks Table
    Schema::create('subtasks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('task_id')->constrained()->onDelete('cascade');
        $table->string('content');
        $table->boolean('is_done')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('cost');
    });
    Schema::dropIfExists('subtasks');
    }
};
