<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->helper('url');
    }

    public function index()
    {
        // Laboratory 5 uses the products table; keep this sample directory available without a users table.
        $users = [
            ['id' => 1, 'firstname' => 'Lloyd Jedrick', 'lastname' => 'Abdon', 'email' => 'lloyd.abdon@example.com', 'username' => 'lloydjedrick'],
            ['id' => 2, 'firstname' => 'Robert', 'lastname' => 'Downey Jr.', 'email' => 'robert.downey@gmail.com', 'username' => 'robertdowneyjr'],
            ['id' => 3, 'firstname' => 'Chris', 'lastname' => 'Evans', 'email' => 'chris.evans@gmail.com', 'username' => 'chrisevans'],
            ['id' => 4, 'firstname' => 'Chris', 'lastname' => 'Hemsworth', 'email' => 'chris.hemsworth@gmail.com', 'username' => 'chrishemsworth'],
            ['id' => 5, 'firstname' => 'Scarlett', 'lastname' => 'Johansson', 'email' => 'scarlett.johansson@gmail.com', 'username' => 'scarlettjohansson'],
        ];

        $this->call->view('users_index', [
            'page_title' => "Lloyd's Student Signal",
            'users' => $users,
        ]);
    }
}
