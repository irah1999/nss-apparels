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
        $templateModel = new \App\Models\TemplateModel();

        $customers = $customerModel->orderBy('name', 'ASC')->findAll();
        $templates = $templateModel->where('status', 'APPROVED')->findAll();

        $data = [
            'title' => 'Customers with Chat',
            'customers' => $customers,
            'templates' => $templates
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
        $templateId = $this->request->getPost('template_id'); // Added this
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

        return $this->processWhatsapp($id, $message, $attachments ?: null, $templateId);
    }

    private function processWhatsapp($customerId, $message, $attachments = null, $templateId = null, $params = null, $headerMedia = null)
    {
        $customerModel = new \App\Models\CustomerModel();
        $logModel = new \App\Models\WhatsappLogModel();
        $templateModel = new \App\Models\TemplateModel();

        $customer = $customerModel->find($customerId);
        $token = env('WHATSAPP_API_TOKEN');
        $phoneId = env('WHATSAPP_PHONE_NUMBER_ID');
        $version = env('WHATSAPP_VERSION', 'v21.0');

        if (!$customer || !$token || !$phoneId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'API Configuration missing']);
        }

        // Auto-replace {name} in message
        if ($message) {
            $message = str_replace('{name}', $customer['name'], $message);
        }

        $baseUrl = "https://graph.facebook.com/{$version}/{$phoneId}/messages";
        $results = [];

        // CASE 1: Sending Template (Marketing / Bulk)
        if ($templateId) {
            $template = $templateModel->find($templateId);
            if ($template) {
                $components = [];

                // Header Component
                if ($headerMedia) {
                    $mediaUrl = base_url('uploads/whatsapp/' . $headerMedia);
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => ['link' => $mediaUrl]
                            ]
                        ]
                    ];
                }

                // Body Component (Parameters)
                if ($params) {
                    $paramArr = is_string($params) ? json_decode($params, true) : $params;
                    if (!empty($paramArr)) {
                        $pElements = [];
                        foreach ($paramArr as $p) {
                            $val = str_replace('{name}', $customer['name'], (string)$p);
                            $pElements[] = ['type' => 'text', 'text' => $val];
                        }
                        $components[] = [
                            'type' => 'body',
                            'parameters' => $pElements
                        ];
                    }
                }

                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $customer['phone'],
                    'type' => 'template',
                    'template' => [
                        'name' => $template['template_name'],
                        'language' => ['code' => $template['language']],
                        'components' => $components
                    ]
                ];
                $results[] = $this->executeCurl($baseUrl, $token, $payload);
            }
        }
        // CASE 2: Free-form text
        elseif (!empty($message)) {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'               => $customer['phone'],
                'type'             => 'text',
                'text'             => ['body' => $message]
            ];
            $results[] = $this->executeCurl($baseUrl, $token, $payload);
        }

        // CASE 3: Media
        if ($attachments) {
            foreach ($attachments as $file) {
                $mime = $file['type'];
                $type = 'document'; // default

                if (strpos($mime, 'image/') === 0) {
                    $type = 'image';
                } elseif (strpos($mime, 'video/') === 0) {
                    $type = 'video';
                } elseif (strpos($mime, 'audio/') === 0) {
                    $type = 'audio';
                }

                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $customer['phone'],
                    'type' => $type
                ];

                // Detect public URL for media if on localhost (important for ngrok)
                $mediaLink = base_url('uploads/whatsapp/' . $file['name']);
                if (strpos($mediaLink, 'localhost') !== false || strpos($mediaLink, '127.0.0.1') !== false) {
                    $publicHost = $this->request->getServer('HTTP_X_FORWARDED_HOST') ?: $this->request->getServer('HTTP_HOST');
                    if ($publicHost && strpos($publicHost, 'localhost') === false) {
                        $protocol = $this->request->getServer('HTTP_X_FORWARDED_PROTO') ?: 'http';
                        $mediaLink = $protocol . '://' . rtrim($publicHost, '/') . '/uploads/whatsapp/' . $file['name'];
                    }
                }

                $payload[$type] = [
                    'link' => $mediaLink
                ];

                // Audio doesn't support caption
                if ($type !== 'audio') {
                    $payload[$type]['caption'] = $file['original_name'];
                }

                $results[] = $this->executeCurl($baseUrl, $token, $payload);
            }
        }

        $success = false;
        $msgIds = [];
        foreach ($results as $res) {
            if ($res['status'] === 'success') {
                $success = true;
                if (isset($res['data']['messages'][0]['id'])) {
                    $msgIds[] = $res['data']['messages'][0]['id'];
                }
            }
        }

        $logModel->insert([
            'customer_id' => $customerId,
            'message'     => $message ?: ($templateId ? "Template: " . ($template['template_name'] ?? '') : 'Media Message'),
            'attachment'  => $attachments ? json_encode(array_column($attachments, 'name')) : null,
            'attachment_type' => $attachments ? json_encode(array_column($attachments, 'type')) : null,
            'direction'   => 'outbound',
            'message_id'  => $msgIds ? implode(',', $msgIds) : null,
            'status'      => $success ? 'sent' : 'failed',
            'response_log' => json_encode($results),
            'sent_at'     => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Message sent' : 'Failed to send',
            'details' => $results
        ]);
    }

    private function executeCurl($url, $token, $payload)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['status' => 'error', 'message' => $error];
        }

        $resData = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['status' => 'success', 'data' => $resData];
        } else {
            return ['status' => 'error', 'message' => $resData['error']['message'] ?? 'API Error', 'code' => $httpCode];
        }
    }

    public function sendWhatsapp()
    {
        $id = $this->request->getPost('customer_id');
        $message = $this->request->getPost('message');
        $templateId = $this->request->getPost('template_id');
        $params = $this->request->getPost('params');
        $headerImage = $this->request->getFile('header_image');

        $processedHeader = null;
        if ($headerImage && $headerImage->isValid() && !$headerImage->hasMoved()) {
            $newName = $headerImage->getRandomName();
            $headerImage->move(ROOTPATH . 'public/uploads/whatsapp', $newName);
            $processedHeader = $newName;
        }

        return $this->processWhatsapp($id, $message, null, $templateId, $params, $processedHeader);
    }

    public function bulkWhatsapp()
    {
        $message = $this->request->getPost('message');
        $templateId = $this->request->getPost('template_id');
        $params = $this->request->getPost('params');
        $files = $this->request->getFileMultiple('attachment');
        $headerImage = $this->request->getFile('header_image');

        $processedHeader = null;
        if ($headerImage && $headerImage->isValid() && !$headerImage->hasMoved()) {
            $newName = $headerImage->getRandomName();
            $headerImage->move(ROOTPATH . 'public/uploads/whatsapp', $newName);
            $processedHeader = $newName;
        }

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
        $customers = $customerModel->findAll(); // Or filter by status if needed

        $count = 0;
        foreach ($customers as $customer) {
            $this->processWhatsapp($customer['id'], $message, $attachments ?: null, $templateId, $params, $processedHeader);
            $count++;
        }

        return $this->response->setJSON(['status' => 'success', 'message' => "Bulk message processed for $count customers"]);
    }
}
