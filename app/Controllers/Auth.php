<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $userModel = new \App\Models\UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            $sessionData = [
                'user_id'   => $user['id'],
                'name'      => $user['name'],
                'email'     => $user['email'],
                'role'      => $user['role'],
                'logged_in' => true,
            ];
            session()->set($sessionData);
            return redirect()->to('/dashboard');
        } else {
            return redirect()->back()->with('error', 'Invalid Email or Password');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
