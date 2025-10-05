<?php

namespace App\Http\Controllers;

use App\Models\Script;
use Illuminate\Http\Request;

class DashboardManager extends Controller
{
    public function index(){
       $scripts =  Script::where('user_id',auth()->id())->get();

        return view('dashboard',compact('scripts'));
    }
}
