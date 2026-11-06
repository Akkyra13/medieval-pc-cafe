<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $req)
    {
        $page = $req->query('page', 'dashboard');

        //array of allowed pages
        //'query param' => 'blade view file'
        $views = [
            'myaccount' => 'myaccount',
            'billing' => 'bill',
            'map' => 'map',
        ];

        //default
        $output = $views[$page] ?? 'dashboard';

        // output
        return view($output, [
            'currentPage' => $page, //access passed to the view with $currentPage
        ]);
    }
}
