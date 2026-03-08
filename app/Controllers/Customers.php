<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Customers extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Customer Management'
        ];
        return view('customers/index', $data);
    }

    public function list()
    {
        $request = service('request');
        $customerModel = new \App\Models\CustomerModel();

        $draw = $request->getPost('draw');
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        $orderColumnIndex = $request->getPost('order')[0]['column'];
        $orderColumnName = $request->getPost('columns')[$orderColumnIndex]['data'];
        $orderDir = $request->getPost('order')[0]['dir'];

        $builder = $customerModel->builder();

        // Total records
        $totalRecords = $builder->countAllResults(false);

        // Search
        if ($searchValue) {
            $builder->groupStart()
                ->like('name', $searchValue)
                ->orLike('email', $searchValue)
                ->orLike('phone', $searchValue)
                ->groupEnd();
        }

        // Total records after filter
        $totalRecordsWithFilter = $builder->countAllResults(false);

        // Ordering and pagination
        $data = $builder->orderBy($orderColumnName, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResult();

        $response = [
            "draw"            => intval($draw),
            "recordsTotal"    => $totalRecords,
            "recordsFiltered" => $totalRecordsWithFilter,
            "data"            => $data
        ];

        return $this->response->setJSON($response);
    }

    public function save()
    {
        $customerModel = new \App\Models\CustomerModel();
        $id = $this->request->getPost('id');
        $data = [
            'name'         => $this->request->getPost('name'),
            'email'        => $this->request->getPost('email'),
            'phone'        => $this->request->getPost('phone'),
            'joining_date' => $this->request->getPost('joining_date'),
        ];

        if ($id) {
            $customerModel->update($id, $data);
            $msg = 'Customer updated successfully';
        } else {
            $customerModel->insert($data);
            $msg = 'Customer added successfully';
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }

    public function delete()
    {
        $customerModel = new \App\Models\CustomerModel();
        $id = $this->request->getPost('id');
        $customerModel->delete($id);
        return $this->response->setJSON(['status' => 'success', 'message' => 'Customer deleted successfully']);
    }

    public function import()
    {
        $file = $this->request->getFile('csv_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($file->getExtension() !== 'csv') {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Please upload a valid CSV file']);
            }

            $customerModel = new \App\Models\CustomerModel();
            $handle = fopen($file->getTempName(), 'r');
            $header = fgetcsv($handle); // Skip header

            $count = 0;
            while (($row = fgetcsv($handle)) !== FALSE) {
                $data = [
                    'name'         => $row[0] ?? '',
                    'email'        => $row[1] ?? '',
                    'phone'        => $row[2] ?? '',
                    'joining_date' => $row[3] ?? date('Y-m-d'),
                ];
                $customerModel->insert($data);
                $count++;
            }
            fclose($handle);

            return $this->response->setJSON(['status' => 'success', 'message' => "$count customers imported successfully"]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'File upload failed']);
    }

    public function chat()
    {
        $customerModel = new \App\Models\CustomerModel();
        $customers = $customerModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Customers with Chat',
            'customers' => $customers
        ];
        return view('customers/chat', $data);
    }

    public function getChatHistory($customerId)
    {
        $logModel = new \App\Models\WhatsappLogModel();
        $history = $logModel->where('customer_id', $customerId)
            ->orderBy('sent_at', 'ASC')
            ->findAll();

        return $this->response->setJSON($history);
    }

    public function sendChat()
    {
        $id = $this->request->getPost('customer_id');
        $message = $this->request->getPost('message');
        $files = $this->request->getFileMultiple('attachment');

        $attachments = [];
        if ($files) {
            $count = 0;
            foreach ($files as $file) {
                if ($count >= 3) break;
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/uploads/whatsapp', $newName);
                    $attachments[] = [
                        'name' => $newName,
                        'type' => $file->getClientMimeType(),
                        'original_name' => $file->getClientName()
                    ];
                    $count++;
                }
            }
        }

        return $this->processWhatsapp($id, $message, $attachments ?: null);
    }

    private function processWhatsapp($customerId, $message, $attachments = null)
    {
        $customerModel = new \App\Models\CustomerModel();
        $configModel = new \App\Models\ConfigModel();
        $logModel = new \App\Models\WhatsappLogModel();

        $customer = $customerModel->find($customerId);
        $token = $configModel->where('config_key', 'whatsapp_token')->first()['config_value'] ?? '';
        $phoneId = $configModel->where('config_key', 'whatsapp_api_id')->first()['config_value'] ?? '';

        $attachmentField = $attachments ? json_encode(array_column($attachments, 'name')) : null;
        $typeField = $attachments ? json_encode(array_column($attachments, 'type')) : null;

        if (!$customer || !$token || !$phoneId) {
            $logModel->insert([
                'customer_id' => $customerId,
                'message'     => $message,
                'attachment'  => $attachmentField,
                'attachment_type' => $typeField,
                'status'      => 'failed',
                'response_log' => 'Missing API Configuration',
                'sent_at'     => date('Y-m-d H:i:s')
            ]);
            return $this->response->setJSON(['status' => 'error', 'message' => 'API Configuration missing']);
        }

        // Real API Call logic would iterate through attachments

        $logModel->insert([
            'customer_id' => $customerId,
            'message'     => $message,
            'attachment'  => $attachmentField,
            'attachment_type' => $typeField,
            'status'      => 'sent',
            'response_log' => 'Simulated success',
            'sent_at'     => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Message sent successfully']);
    }

    public function sendWhatsapp()
    {
        $id = $this->request->getPost('customer_id');
        $message = $this->request->getPost('message');
        return $this->processWhatsapp($id, $message);
    }

    public function bulkWhatsapp()
    {
        $message = $this->request->getPost('message');
        $files = $this->request->getFileMultiple('attachment');

        $attachments = [];
        if ($files) {
            $count = 0;
            foreach ($files as $file) {
                if ($count >= 3) break;
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(ROOTPATH . 'public/uploads/whatsapp', $newName);
                    $attachments[] = [
                        'name' => $newName,
                        'type' => $file->getClientMimeType(),
                        'original_name' => $file->getClientName()
                    ];
                    $count++;
                }
            }
        }

        $customerModel = new \App\Models\CustomerModel();
        $customers = $customerModel->where('status', 1)->findAll();

        $count = 0;
        foreach ($customers as $customer) {
            $this->processWhatsapp($customer['id'], $message, $attachments ?: null);
            $count++;
        }

        return $this->response->setJSON(['status' => 'success', 'message' => "Bulk message processed for $count customers"]);
    }
}
