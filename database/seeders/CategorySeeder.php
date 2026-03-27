<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Breakfast',
            'Lunch',
            'Dinner',
            'Snack',
            'Smoothie',
            'Drinks',
            'Dessert',
            'Vegetarian',
            'Other',
        ];

        foreach ($categories as $name) {
            DB::table('categories')->insertOrIgnore([
                'id'   => Str::uuid(),
                'name' => $name,
                'slug' => Str::slug($name),   // breakfast, lunch, dinner...
            ]);
        }
    }
}
