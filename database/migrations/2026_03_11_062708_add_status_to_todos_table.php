<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->enum('status', ['pending','in_progress','completed','archived'])
                  ->default('pending')
                  ->after('priority');
            $table->enum('recurring', ['none','daily','weekly','monthly'])
                  ->default('none')
                  ->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('recurring');
        });
    }
};