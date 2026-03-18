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
            if ($customerModel->update($id, $data) === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => implode(', ', $customerModel->errors())]);
            }
            $msg = 'Customer updated successfully';
        } else {
            if ($customerModel->insert($data) === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => implode(', ', $customerModel->errors())]);
            }
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

            $uuid = 'imp_' . time() . '_' . rand(1000, 9999);
            $dir = WRITEPATH . 'uploads/imports/';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            // Move file to imports folder
            $file->move($dir, $uuid . '.csv');

            // Log entry into database
            $bulkModel = new \App\Models\BulkImportModel();
            $bulkModel->insert([
                'file_name'      => $file->getClientName(),
                'filepath'       => $uuid . '.csv',
                'total_count'    => 0,
                'inserted_count' => 0,
                'failed_count'   => 0,
                'status'         => 'processing'
            ]);

            // Set initial state for cache fallback
            cache()->save('import_' . $uuid, ['total' => 0, 'current' => 0, 'done' => false], 1200);

            // Trigger background shell command
            $cmd = '"' . PHP_BINARY . '" "' . ROOTPATH . 'spark" import:customers ' . $uuid;
            
            $logFile = WRITEPATH . 'import_debug.log';
            file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Controller triggering command for: $uuid\n", FILE_APPEND);
            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec('start /B "" ' . $cmd);
            } else {
                exec($cmd . " > /dev/null 2>&1 &");
            }

            return $this->response->setJSON([
                'status' => 'success', 
                'import_id' => $uuid, 
                'message' => 'Import queued. You can review progress inside the History Logs Dashboard shortly.'
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'File upload failed']);
    }

    public function getImports()
    {
        $bulkModel = new \App\Models\BulkImportModel();
        $imports = $bulkModel->orderBy('created_at', 'DESC')->limit(10)->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $imports]);
    }

    public function downloadImportFile($id)
    {
        $bulkModel = new \App\Models\BulkImportModel();
        $row = $bulkModel->find($id);
        if (!$row) exit('File not found');

        $filepath = WRITEPATH . 'uploads/imports/' . $row['filepath'];
        if (!file_exists($filepath)) exit('File does not exist on server.');

        return $this->response->download($filepath, null)->setFileName((string)$row['file_name']);
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
        
        $limit = (int) $this->request->getGet('limit') ?: 50;
        $offset = (int) $this->request->getGet('offset') ?: 0;
        $date = $this->request->getGet('date'); 
        $order = $this->request->getGet('order') ?: 'DESC';

        $builder = $logModel->where('customer_id', $customerId);

        if (!empty($date)) {
            $builder->where('DATE(sent_at)', $date);
        }

        $totalRecords = $builder->countAllResults(false); 

        // Apply order configuration (ASC or DESC)
        $history = $builder->orderBy('sent_at', $order)
                           ->limit($limit, $offset)
                           ->findAll();

        return $this->response->setJSON([
            'data' => $history,
            'totalRecords' => $totalRecords,
            'limit' => $limit,
            'offset' => $offset,
            'totalPages' => ceil($totalRecords / $limit)
        ]);
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

    public function downloadSample()
    {
        $filename = 'customers_sample.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Name', 'Email', 'Phone', 'Joining Date']);
        fputcsv($output, ['John Doe', 'john@example.com', '919876543210', '2025-01-01']);
        fclose($output);
        exit;
    }

    public function export()
    {
        $type = $this->request->getPost('type') ?? 'csv';
        $id = $this->request->getPost('id') ?? 'export_' . time();
        
        $customerModel = new \App\Models\CustomerModel();
        $total = $customerModel->countAllResults();
        
        cache()->save('export_' . $id, ['total' => $total, 'current' => 0, 'done' => false], 600);
        
        if ($type === 'xlsx') {
            return $this->exportXlsx($customerModel, $id, $total);
        } else {
            return $this->exportCsv($customerModel, $id, $total);
        }
    }

    public function getExportProgress($id)
    {
        $data = cache('export_' . $id);
        if (!$data) return $this->response->setJSON(['status' => 'success', 'percent' => 0]);
        
        $percent = $data['total'] > 0 ? ($data['current'] / $data['total']) * 100 : 0;
        return $this->response->setJSON([
            'status' => 'success',
            'percent' => round($percent),
            'current' => $data['current'],
            'total' => $data['total']
        ]);
    }

    private function exportCsv($model, $id, $total)
    {
        $filename = 'customers_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Joining Date']);
        
        $current = 0;
        $model->chunk(500, function ($customer) use ($output, &$current, $id, $total) {
            fputcsv($output, [
                $customer['id'],
                $customer['name'],
                $customer['email'] ?: '',
                $customer['phone'],
                $customer['joining_date']
            ]);
            $current++;
            if ($current % 50 === 0) {
                cache()->save('export_' . $id, ['total' => $total, 'current' => $current, 'done' => false], 600);
            }
        });
        
        cache()->save('export_' . $id, ['total' => $total, 'current' => $total, 'done' => true], 600);
        fclose($output);
        exit;
    }

    private function exportXlsx($model, $id, $total)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Phone');
        $sheet->setCellValue('E1', 'Joining Date');
        
        $rowNumber = 2; // Start from row 2
        $current = 0;
        
        $model->chunk(500, function ($customer) use (&$sheet, &$rowNumber, &$current, $id, $total) {
            $sheet->setCellValue('A' . $rowNumber, $customer['id']);
            $sheet->setCellValue('B' . $rowNumber, $customer['name']);
            $sheet->setCellValue('C' . $rowNumber, $customer['email'] ?: '');
            
            // Set cell value as explicit string to fix scientific notation
            $sheet->setCellValueExplicit(
                'D' . $rowNumber, 
                (string) $customer['phone'] . ' ', 
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            
            $sheet->setCellValue('E' . $rowNumber, $customer['joining_date']);
            $rowNumber++;
            $current++;
            if ($current % 50 === 0) {
                cache()->save('export_' . $id, ['total' => $total, 'current' => $current, 'done' => false], 600);
            }
        });
        
        cache()->save('export_' . $id, ['total' => $total, 'current' => $total, 'done' => true], 600);
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'customers_' . date('Ymd_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($filename) . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}
