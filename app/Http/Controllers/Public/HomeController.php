<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $data = include resource_path('views/data/placeholder.php');

        $categories = collect($data['categories']);
        $featuredBusinesses = collect($data['businesses'])
            ->filter(fn($b) => $b['featured'] ?? false)
            ->take(4)
            ->values()
            ->all();

        return view('public.home.index', [
            'categories' => $categories,
            'featuredBusinesses' => $featuredBusinesses,
        ]);
    }
}
