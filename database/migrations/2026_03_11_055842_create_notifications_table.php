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
Schema::create('notifications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('todo_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title');
    $table->text('message');
    $table->enum('type', ['due_soon','overdue','completed','reminder'])->default('reminder');
    $table->boolean('read')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
