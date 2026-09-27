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
        try {
            $this->call->database();
            $this->call->model('UsersModel');
            $users = $this->UsersModel->all();
        } catch (Throwable $error) {
            $users = [];
        }

        $this->call->view('users_index', [
            'page_title' => "Lloyd's Student Signal",
            'users' => $users,
        ]);
    }
}
