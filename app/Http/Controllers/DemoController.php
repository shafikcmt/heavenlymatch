<?php
namespace App\Http\Controllers;

use App\Models\Biodata;

class DemoController extends Controller
{
    public function index()
    {
        // Eloquent automatically performs the join internally
        $data = Biodata::with('registration')->get();

        if ($data->isEmpty()) {
            return "No biodata found. Check your table linkage.";
        }

        return view('pages.user-dashboard.demo', compact('data'));
    }
}
