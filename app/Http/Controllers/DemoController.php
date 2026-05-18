<?php

namespace App\Http\Controllers;

use App\Models\Biodata;

class DemoController extends Controller
{
    public function index()
    {
        $data = Biodata::with('registration')->latest()->take(24)->get();

        return view('pages.user-dashboard.demo', compact('data'));
    }
}
