<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Home',
            'user' => 'ivan'
        ];
       return view('Front/Home/index', $data);
    }

   
}
