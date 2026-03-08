<?php

namespace App\Controllers;

use App\Models\EnquiryModel;

class Contact extends BaseController
{
    public function index()
    {
        $data['title'] = 'Contact Us | NSS APPARELS';
        return view('contact', $data);
    }

    public function submit()
    {
        $rules = [
            'name'        => 'required|min_length[3]|max_length[255]',
            'email'       => 'required|valid_email|max_length[255]',
            'description' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new EnquiryModel();

        $data = [
            'name'        => $this->request->getPost('name'),
            'email'       => $this->request->getPost('email'),
            'description' => $this->request->getPost('description'),
        ];

        if ($model->insert($data)) {
            return redirect()->to('contact')->with('success', 'Thank you for contacting us! We will get back to you soon.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
