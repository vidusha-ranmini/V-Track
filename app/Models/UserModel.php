<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = ['user_name', 'password'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $returnType = 'array';

    public function getUserByUsername($username)
    {
        return $this->where('user_name', $username)->first();
    }

    public function getPasswordByUsername($username)
    {
        $user = $this->select('password')->where('user_name', $username)->first();
        return $user ? $user['password'] : null;
    }
}
