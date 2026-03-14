<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TemplateModel;

class Templates extends BaseController
{
    public function index()
    {
        $model = new TemplateModel();
        $templates = $model->orderBy('id', 'DESC')->findAll();

        $data = [
            'title' => 'WhatsApp Templates',
            'templates' => $templates
        ];
        return view('templates/index', $data);
    }

    public function sync()
    {
        $token = env('WHATSAPP_API_TOKEN');
        $wabaId = env('WHATSAPP_BUSINESS_ACCOUNT_ID');
        $version = env('WHATSAPP_VERSION', 'v22.0');

        if (!$token || !$wabaId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'API Configuration missing in .env']);
        }

        $url = "https://graph.facebook.com/{$version}/{$wabaId}/message_templates?limit=100";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $metaData = json_decode($response, true);
        if ($httpCode != 200) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to fetch from Meta', 'details' => $metaData]);
        }

        $model = new TemplateModel();
        foreach ($metaData['data'] as $tpl) {
            $existing = $model->where('template_name', $tpl['name'])->first();

            $body = '';
            $footer = '';
            $header_type = 'NONE';

            foreach ($tpl['components'] as $comp) {
                if ($comp['type'] == 'BODY') $body = $comp['text'];
                if ($comp['type'] == 'FOOTER') $footer = $comp['text'];
                if ($comp['type'] == 'HEADER') $header_type = $comp['format'] ?? 'TEXT';
            }

            $data = [
                'template_name' => $tpl['name'],
                'language' => $tpl['language'],
                'category' => $tpl['category'],
                'status' => $tpl['status'],
                'body_text' => $body,
                'meta_template_id' => $tpl['id'] ?? null
            ];

            if ($existing) {
                $model->update($existing['id'], $data);
            } else {
                $model->insert($data);
            }
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Templates synced successfully']);
    }

    /**
     * Upload media to Meta to get a handle for Template Creation
     */
    public function uploadMedia()
    {
        $file = $this->request->getFile('file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid file']);
        }

        $newName = $file->getRandomName();
        $file->move(ROOTPATH . 'public/uploads/whatsapp', $newName);

        $mediaLink = base_url('uploads/whatsapp/' . $newName);

        // Force replace localhost with ngrok public URL
        if (strpos($mediaLink, 'localhost') !== false || strpos($mediaLink, '127.0.0.1') !== false) {
            // Call ngrok local API to get the public URL dynamically
            $ch = curl_init('http://127.0.0.1:4040/api/tunnels');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            $ngrokRes = curl_exec($ch);
            curl_close($ch);

            if ($ngrokRes) {
                $ngrokData = json_decode($ngrokRes, true);
                if (!empty($ngrokData['tunnels'][0]['public_url'])) {
                    $publicUrl = rtrim($ngrokData['tunnels'][0]['public_url'], '/');
                    // Replace base_url domain with ngrok domain
                    $parsedUrl = parse_url($mediaLink);
                    $path = $parsedUrl['path'] ?? '/uploads/whatsapp/' . $newName;
                    $mediaLink = $publicUrl . $path;
                }
            }
        }

        return $this->response->setJSON(['status' => 'success', 'url' => $mediaLink]);
    }

    public function create()
    {
        $token = env('WHATSAPP_API_TOKEN');
        $wabaId = env('WHATSAPP_BUSINESS_ACCOUNT_ID');
        $version = env('WHATSAPP_VERSION', 'v22.0');

        $name = strtolower(str_replace(' ', '_', $this->request->getPost('name')));
        $category = $this->request->getPost('category');
        $language = $this->request->getPost('language') ?: 'en';

        $components = [];

        // HEADER
        $headerType = $this->request->getPost('header_type');
        if ($headerType == 'IMAGE') {
            $url = $this->request->getPost('header_url');
            $components[] = [
                'type' => 'HEADER',
                'format' => 'IMAGE',
                'example' => [
                    'header_url' => [$url]
                ]
            ];
        }

        // BODY
        $bodyText = $this->request->getPost('body');
        $bodyExamples = $this->request->getPost('body_examples'); // Expecting array

        $bodyComp = [
            'type' => 'BODY',
            'text' => $bodyText
        ];

        if (!empty($bodyExamples)) {
            $bodyComp['example'] = [
                'body_text' => [$bodyExamples]
            ];
        }
        $components[] = $bodyComp;

        // FOOTER
        $footerText = $this->request->getPost('footer');
        if ($footerText) {
            $components[] = [
                'type' => 'FOOTER',
                'text' => $footerText
            ];
        }

        // BUTTONS
        $buttonText = $this->request->getPost('button_text');
        $buttonUrl = $this->request->getPost('button_url');
        if ($buttonText && $buttonUrl) {
            $components[] = [
                'type' => 'BUTTONS',
                'buttons' => [
                    [
                        'type' => 'URL',
                        'text' => $buttonText,
                        'url' => $buttonUrl
                    ]
                ]
            ];
        }

        $payload = [
            'name' => $name,
            'language' => $language,
            'category' => $category,
            'components' => $components
        ];

        $url = "https://graph.facebook.com/{$version}/{$wabaId}/message_templates";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resData = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            // Log to local DB and sync
            $this->sync();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Template created successfully']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => $resData['error']['message'] ?? 'Meta API Error', 'payload' => $payload]);
        }
    }
}
