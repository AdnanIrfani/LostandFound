<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemMatchesTable extends Migration
{
    public function up()
    {
        Schema::create('item_matches', function (Blueprint $table) {
            $table->id('match_id');
            $table->foreignId('lost_id')->constrained('lost_items', 'lost_id')->onDelete('cascade');
            $table->foreignId('found_id')->constrained('found_items', 'found_id')->onDelete('cascade');
            $table->foreignId('matched_by_user_id')->nullable()->constrained('users', 'user_id');
            $table->decimal('similarity_score', 5, 2);
            $table->text('match_reason')->nullable();
            $table->enum('match_status', ['pending', 'review', 'verified', 'rejected', 'completed'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users', 'user_id');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamp('handover_date')->nullable();
            $table->text('handover_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint to prevent duplicate matches
            $table->unique(['lost_id', 'found_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_matches');
    }
}