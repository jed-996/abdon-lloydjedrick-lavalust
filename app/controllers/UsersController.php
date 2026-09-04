<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class UsersController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->model('UsersModel');
        $this->call->helper('url');
    }

    public function index()
    {
        $this->call->view('users_index', [
            'page_title' => "Lloyd's Student Signal",
            'users' => $this->UsersModel->all(),
        ]);
    }
}
