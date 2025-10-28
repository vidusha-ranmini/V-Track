<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    public function getUserByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getPasswordByUsername($username)
    {
        $user = $this->select('password')->where('username', $username)->first();
        return $user ? $user['password'] : null;
    }
}
