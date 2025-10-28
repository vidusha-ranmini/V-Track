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
        // Debug: Log the database file path being used
        $db = \Config\Database::connect();
        $dbPath = $db->getDatabase();
        log_message('debug', 'Database file path: ' . $dbPath);
        $userModel = new UserModel();
        $user = $userModel->getUserByUsername($username);

        // The database stores plain-text passwords; compare directly.
        if ($user && isset($user['password']) && $user['password'] === $password) {
            $session->set(['isLoggedIn' => true, 'username' => $username]);
            return redirect()->to(base_url('sidebar'));
        } else {
            // Log more details for debugging without exposing the actual password value
            $found = $user ? 'yes' : 'no';
            $storedPw = ($user && isset($user['password'])) ? 'present' : 'missing';
            log_message('error', 'Login failed for username: ' . $username . '. User found: ' . $found . ', Stored password: ' . $storedPw);
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
