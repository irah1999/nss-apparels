<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        $customerModel = new \App\Models\CustomerModel();
        $userModel = new \App\Models\UserModel();

        $data = [
            'total_customers' => $customerModel->countAll(),
            'total_users'     => $userModel->countAll(),
            'title'           => 'Dashboard'
        ];

        return view('dashboard/index', $data);
    }
}
