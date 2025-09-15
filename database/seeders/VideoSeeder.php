<?php
namespace Database\Seeders;

use App\Models\Video;
use App\Models\Category;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $tecnologiaCategory = Category::where('slug', 'tecnologia')->first();
        $educacaoCategory = Category::where('slug', 'educacao')->first();
        $entretenimentoCategory = Category::where('slug', 'entretenimento')->first();

        $videos = [
            // Tecnologia
            [
                'category_id' => $tecnologiaCategory->id,
                'title' => 'Introdução ao PHP Moderno',
                'slug' => 'introducao-php-moderno',
                'description' => 'Aprenda os conceitos fundamentais do PHP 8+ e suas principais novidades.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '15:30',
                'level' => 'iniciante',
                'is_premium' => true,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $tecnologiaCategory->id,
                'title' => 'Desenvolvimento Web Avançado',
                'slug' => 'desenvolvimento-web-avancado',
                'description' => 'Técnicas avançadas para criar aplicações web robustas e escaláveis.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '22:45',
                'level' => 'avancado',
                'is_premium' => true,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $tecnologiaCategory->id,
                'title' => 'APIs RESTful com PHP',
                'slug' => 'apis-restful-php',
                'description' => 'Como criar e consumir APIs RESTful usando PHP e melhores práticas.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '18:20',
                'level' => 'intermediario',
                'is_premium' => true,
                'is_published' => true,
                'sort_order' => 3,
            ],

            // Educação
            [
                'category_id' => $educacaoCategory->id,
                'title' => 'Métodos de Estudo Eficazes',
                'slug' => 'metodos-estudo-eficazes',
                'description' => 'Descubra técnicas comprovadas para otimizar seu aprendizado.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '12:15',
                'level' => 'iniciante',
                'is_premium' => false, // Vídeo gratuito
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $educacaoCategory->id,
                'title' => 'Gestão do Tempo para Estudos',
                'slug' => 'gestao-tempo-estudos',
                'description' => 'Organize sua rotina de estudos de forma mais eficiente.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '16:40',
                'level' => 'iniciante',
                'is_premium' => true,
                'is_published' => true,
                'sort_order' => 2,
            ],

            // Entretenimento
            [
                'category_id' => $entretenimentoCategory->id,
                'title' => 'Top 10 Filmes Sci-Fi',
                'slug' => 'top-10-filmes-sci-fi',
                'description' => 'Uma seleção dos melhores filmes de ficção científica da década.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '25:10',
                'level' => 'livre',
                'is_premium' => true,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $entretenimentoCategory->id,
                'title' => 'Análise: O Futuro dos Games',
                'slug' => 'analise-futuro-games',
                'description' => 'Tendências e inovações que vão revolucionar a indústria dos jogos.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '20:35',
                'level' => 'intermediario',
                'is_premium' => true,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => $entretenimentoCategory->id,
                'title' => 'Música Eletrônica Brasileira',
                'slug' => 'musica-eletronica-brasileira',
                'description' => 'Um panorama da cena eletrônica nacional e seus principais artistas.',
                'youtube_id' => 'sDpFK8BtstM',
                'duration' => '30:25',
                'level' => 'livre',
                'is_premium' => false, // Vídeo gratuito
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($videos as $video) {
            Video::create($video);
        }
    }
}
