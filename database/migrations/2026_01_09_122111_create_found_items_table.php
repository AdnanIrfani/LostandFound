// database/migrations/xxxx_create_found_items_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoundItemsTable extends Migration
{
    public function up()
    {
        Schema::create('found_items', function (Blueprint $table) {
            $table->id('found_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories', 'category_id');
            $table->string('item_name');
            $table->text('description');
            $table->date('found_date');
            $table->time('found_time')->nullable();
            $table->string('found_location');
            $table->string('building')->nullable();
            $table->string('room_number')->nullable();
            $table->string('item_image')->nullable();
            $table->json('additional_images')->nullable();
            $table->enum('item_condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->string('storage_location')->nullable();
            $table->string('handover_person')->nullable(); // Person who has the item
            $table->string('handover_contact')->nullable(); // Contact of that person
            $table->boolean('handover_to_admin')->default(false);
            $table->enum('status', ['pending', 'unclaimed', 'claimed', 'verified', 'returned'])->default('pending');
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
        Schema::dropIfExists('found_items');
    }
}