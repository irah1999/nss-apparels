<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TemplateModel;

class Templates extends BaseController
{
    public function index()
    {
        $model = new TemplateModel();
        $templates = $model->findAll();

        $data = [
            'title' => 'WhatsApp Templates',
            'templates' => $templates
        ];
        return view('templates/index', $data);
    }

    public function sync()
    {
        $token = env('WHATSAPP_API_TOKEN');
        $wabaId = env('WHATSAPP_BUSINESS_ACCOUNT_ID'); // Need to add this to .env
        $version = env('WHATSAPP_VERSION', 'v21.0');

        if (!$token || !$wabaId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'API Configuration missing in .env']);
        }

        $url = "https://graph.facebook.com/{$version}/{$wabaId}/message_templates";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to fetch from Meta', 'details' => json_decode($response)]);
        }

        $metaData = json_decode($response, true);
        $model = new TemplateModel();

        foreach ($metaData['data'] as $tpl) {
            $existing = $model->where('template_name', $tpl['name'])->first();

            $body = '';
            foreach ($tpl['components'] as $comp) {
                if ($comp['type'] == 'BODY') $body = $comp['text'];
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

    public function create()
    {
        $token = env('WHATSAPP_API_TOKEN');
        $wabaId = env('WHATSAPP_BUSINESS_ACCOUNT_ID');
        $version = env('WHATSAPP_VERSION', 'v21.0');

        $name = $this->request->getPost('name');
        $category = $this->request->getPost('category');
        $body = $this->request->getPost('body');

        $payload = [
            'name' => strtolower(str_replace(' ', '_', $name)),
            'language' => 'en_US',
            'category' => $category,
            'components' => [
                [
                    'type' => 'BODY',
                    'text' => $body
                ]
            ]
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
            // Success, sync again to get local DB updated
            $this->sync();
            return $this->response->setJSON(['status' => 'success', 'message' => 'Template created and submitted for review']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => $resData['error']['message'] ?? 'Meta API Error']);
        }
    }
}
