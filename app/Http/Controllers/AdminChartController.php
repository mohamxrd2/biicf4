<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminChartController extends Controller
{
    //
    public function index(){
        return view('admin.chart');
    }
}
