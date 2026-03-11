<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
// database/migrations/2024_01_01_000003_create_todos_table.php
Schema::create('todos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
    $table->enum('status', ['pending','in_progress','completed','archived'])->default('pending');
    $table->enum('category', ['general','work','personal','health','shopping'])->default('general');
    $table->date('due_date')->nullable();
    $table->time('due_time')->nullable();
    $table->boolean('completed')->default(false);
    $table->enum('recurring', ['none','daily','weekly','monthly'])->default('none');
    $table->integer('position')->default(0);
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};