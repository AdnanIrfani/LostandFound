// database/migrations/xxxx_create_lost_items_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLostItemsTable extends Migration
{
    public function up()
    {
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id('lost_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->string('item_name');
            $table->text('description');
            $table->date('lost_date');
            $table->time('lost_time')->nullable();
            $table->string('lost_location');
            $table->string('building')->nullable(); // College building
            $table->string('room_number')->nullable(); // Room number
            $table->string('item_image')->nullable();
            $table->json('additional_images')->nullable(); // Multiple images
            $table->decimal('reward', 10, 2)->nullable();
            $table->enum('urgency', ['low', 'medium', 'high'])->default('medium');
            $table->date('claim_deadline')->nullable();
            $table->enum('status', ['pending', 'open', 'matched', 'verified', 'recovered', 'closed'])->default('pending');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users', 'user_id');
            $table->integer('view_count')->default(0);
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lost_items');
    }
}