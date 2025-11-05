<?php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Controller;

class Login extends Controller

{

   
    public function index()
    {
         
        return view('login');
    }

    public function authenticate()
    {
        $session = session();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $userModel = new UserModel();
        $user = $userModel->getUserByUsername($username);

        // Verify hashed password
        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            $session->set([
                'isLoggedIn' => true,
                'user_id' => $user['user_id'],
                'username' => $user['user_name']
            ]);
            return redirect()->to(base_url('sidebar'));
        } else {
            log_message('error', 'Login failed for username: ' . $username);
            return view('login', ['error' => 'Invalid username or password']);
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
}
