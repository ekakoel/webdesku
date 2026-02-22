<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $village = app('currentVillage');

        $news = News::where('village_id', $village->id)->latest()->take(3)->get();

        return view('home', compact('village', 'news'));
    }
}
