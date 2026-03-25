<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number')->nullable();
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->string('profile_picture')->nullable();
            $table->string('student_id')->nullable()->unique(); // For college students
            $table->string('department')->nullable(); // Computer Science, Engineering, etc.
            $table->integer('year')->nullable(); // 1st, 2nd, 3rd, 4th year
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->integer('total_lost_items')->default(0);
            $table->integer('total_found_items')->default(0);
            $table->integer('total_recovered_items')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}