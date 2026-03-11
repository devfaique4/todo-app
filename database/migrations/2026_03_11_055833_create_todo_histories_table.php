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
Schema::create('todo_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('todo_id')->constrained()->cascadeOnDelete();
    $table->string('field_changed');
    $table->text('old_value')->nullable();
    $table->text('new_value')->nullable();
    $table->string('action'); // created, updated, deleted
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todo_histories');
    }
};
