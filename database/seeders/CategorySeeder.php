<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tecnologia',
                'slug' => 'tecnologia',
                'description' => 'Cursos de programação, desenvolvimento web e tecnologias emergentes',
                'icon' => 'fas fa-code',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Educação',
                'slug' => 'educacao',
                'description' => 'Métodos de estudo, técnicas de aprendizagem e desenvolvimento pessoal',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#10B981',
                'sort_order' => 2,
            ],
            [
                'name' => 'Entretenimento',
                'slug' => 'entretenimento',
                'description' => 'Conteúdos sobre filmes, jogos, música e cultura pop',
                'icon' => 'fas fa-play-circle',
                'color' => '#F59E0B',
                'sort_order' => 3,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
