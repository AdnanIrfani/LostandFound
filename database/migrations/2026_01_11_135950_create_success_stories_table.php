// database/migrations/xxxx_create_success_stories_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuccessStoriesTable extends Migration
{
    public function up()
    {
        Schema::create('success_stories', function (Blueprint $table) {
            $table->id('story_id');
            $table->foreignId('lost_id')->constrained('lost_items', 'lost_id');
            $table->foreignId('found_id')->constrained('found_items', 'found_id');
            $table->foreignId('match_id')->constrained('item_matches', 'match_id');
            $table->text('recovery_story')->nullable();
            $table->json('recovery_images')->nullable();
            $table->integer('days_to_recover')->nullable();
            $table->enum('satisfaction_rating', [1, 2, 3, 4, 5])->nullable();
            $table->text('testimonial')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('success_stories');
    }
}