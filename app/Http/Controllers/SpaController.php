<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpaController extends Controller
{
    public function index()
    {
        return view('themes.default1.client.layout.client');
    }
}
