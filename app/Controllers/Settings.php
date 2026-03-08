<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Settings extends BaseController
{
    public function index()
    {
        $configModel = new \App\Models\ConfigModel();
        $configs = $configModel->findAll();
        $data = [
            'configs' => $configs,
            'title'   => 'Platform Configuration'
        ];
        return view('settings/index', $data);
    }

    public function save()
    {
        $configModel = new \App\Models\ConfigModel();
        $configs = $this->request->getPost('configs');

        if ($configs) {
            foreach ($configs as $key => $value) {
                $configModel->where('config_key', $key)
                    ->set(['config_value' => $value])
                    ->update();
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
