<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class Pages extends Controller
{
    public function landing()
    {
        return view('landing');
    }
    public function login()
    {
        return view('login');
    }
    public function sidebar()
    {
        return view('sidebar');
    }
    public function add_details()
    {
        return view('add_details');
    }
    public function dashboard()
    {
        return view('dashboard');
    }
    public function view_details()
    {
        return view('view_details');
    }
}
