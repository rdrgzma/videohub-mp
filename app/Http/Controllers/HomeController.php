<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Video;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Carregar categorias com vídeos publicados
        $categories = Category::active()
            ->with(['publishedVideos' => function ($query) {
                $query->ordered()->limit(6);
            }])
            ->ordered()
            ->get();

        // Carregar planos ativos
        $plans = Plan::active()->ordered()->get();

        // Estatísticas básicas
        $stats = [
            'total_videos' => Video::published()->count(),
            'total_students' => 15000,
            'average_rating' => 4.9,
        ];

        return view('home', compact('categories', 'plans', 'stats'));
    }

    public function plans()
    {
        $plans = Plan::active()->ordered()->get();

        return view('plans', compact('plans'));
    }
}
