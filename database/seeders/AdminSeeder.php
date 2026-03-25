<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        User::create([
            'username' => 'admin',
            'email' => 'admin@college.edu',
            'password' => Hash::make('admin123'),
            'phone_number' => '9876543210',
            'role' => 'admin',
            'department' => 'Administration',
            'is_active' => true,
        ]);

        // Create Sample Student User
        User::create([
            'username' => 'student',
            'email' => 'student@college.edu',
            'password' => Hash::make('student123'),
            'phone_number' => '9876543211',
            'role' => 'user',
            'student_id' => 'CS2021001',
            'department' => 'Computer Science',
            'year' => 3,
            'is_active' => true,
        ]);

        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics'],
            ['name' => 'Documents', 'slug' => 'documents'],
            ['name' => 'Accessories', 'slug' => 'accessories'],
            ['name' => 'Clothing', 'slug' => 'clothing'],
            ['name' => 'Books', 'slug' => 'books'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Admin and sample user created successfully!');
        $this->command->info('Admin Credentials: admin@college.edu / admin123');
        $this->command->info('Student Credentials: student@college.edu / student123');
    }
}