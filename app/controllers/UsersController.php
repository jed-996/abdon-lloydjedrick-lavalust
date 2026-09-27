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
        $users = [];

        $this->call->view('users_index', [
            'page_title' => "Lloyd's Student Signal",
            'users' => $users,
        ]);
    }
}
