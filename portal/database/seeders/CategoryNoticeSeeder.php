<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

;

class CategoryNoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $categories = [
            'Esporte',
            'Política',
            'Finanças',
            'Cotidiano',
            'Tecnologia',
            'Saúde',
            'Educação',
            'Entretenimento',
            'Internacional',
            'Meio Ambiente',
        ];

        $users = User::factory(count($categories))->create();

        foreach ($categories as $index => $categoryName) {
            $user = $users[$index];

            $category = Category::create([
                'user_id' => $user->id,
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            Notice::factory(30)->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
