<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentController extends Controller
{
    private function studentData(): array
    {
        return [
            'page_title'  => "Lloyd's Student Signal",
            'student_id'  => 'MCC-2024 00090',
            'name'        => 'Lloyd Jedrick D. Abdon',
            'course'      => 'BS Information Technology',
            'year'        => '3rd Year',
            'section'     => 'F2',
            'email'       => 'lloydjedrickabdon@gmail.com',
            'description' => 'An information technology student exploring web systems, databases, and practical software deployment.',
            'skills'      => ['PHP', 'Git and GitHub', 'Database Design', 'Web Deployment'],
            'hobbies'     => ['Building web projects', 'Learning new technologies', 'Problem solving'],
        ];
    }

    public function index()
    {
        $data = $this->studentData();
        $data['access_notice'] = ($_GET['notice'] ?? '') === 'profile-protected';

        $this->call->view('student_home', $data);
    }

    public function profile()
    {
        $this->call->view('student_profile', $this->studentData());
    }
}
