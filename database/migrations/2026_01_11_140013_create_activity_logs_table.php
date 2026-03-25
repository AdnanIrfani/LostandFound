// database/migrations/xxxx_create_activity_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id');
            $table->string('action'); // created, updated, deleted, verified, etc.
            $table->string('model_type'); // LostItem, FoundItem, Match, etc.
            $table->unsignedBigInteger('model_id');
            $table->text('description');
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['user_id', 'action', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}