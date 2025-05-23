<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        $categories = Category::paginate(8);
        return view('welcome', compact('categories'));
    }
}
