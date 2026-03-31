<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $topics = Topic::where('is_draft', false)
            ->latest()
            ->get();
        $categories = Category::all();

        return response()
            ->view('seo.sitemap', compact('topics', 'categories'))
            ->header('Content-Type', 'text/xml');
    }
}
