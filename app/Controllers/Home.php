<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Selamat Datang!'
        ];

        return view('home', $data);
    }

}