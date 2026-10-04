<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiUserModel extends Model
{
    protected $table = 'users';
    protected $fillable = ['username', 'email', 'password', 'role', 'is_active'];
    protected $timestamps = false;
    protected $has_soft_delete = false;

    public function find_by_email(string $email)
    {
        return $this->db->table($this->table)
            ->where('email', strtolower($email))
            ->limit(1)
            ->get();
    }
}
