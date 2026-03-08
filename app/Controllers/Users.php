<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Users extends BaseController
{
    public function index()
    {
        $userModel = new \App\Models\UserModel();
        $data = [
            'users' => $userModel->findAll(),
            'title' => 'User Management'
        ];
        return view('users/index', $data);
    }

    public function save()
    {
        $userModel = new \App\Models\UserModel();
        $id = $this->request->getPost('id');
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role'  => $this->request->getPost('role'),
        ];

        $password = $this->request->getPost('password');
        if ($password) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        if ($id) {
            $userModel->update($id, $data);
            $msg = 'User updated successfully';
        } else {
            if (!$password) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Password is required for new users']);
            }
            $userModel->insert($data);
            $msg = 'User added successfully';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function delete()
    {
        $userModel = new \App\Models\UserModel();
        $id = $this->request->getPost('id');
        if ($id == session()->get('user_id')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'You cannot delete yourself']);
        }
        $userModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'User deleted successfully']);
    }
}
