<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();
        $data['username'] = $session->get('username');
        $data['role'] = $session->get('role');
        return view('dashboard', $data);
    }
}
