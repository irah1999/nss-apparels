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
            $header_url = '';
            $button_text = '';
            $button_url = '';

            foreach ($tpl['components'] as $comp) {
                if ($comp['type'] == 'BODY') $body = $comp['text'];
                if ($comp['type'] == 'FOOTER') $footer = $comp['text'];
                if ($comp['type'] == 'HEADER') {
                    $header_type = $comp['format'] ?? 'TEXT';

                    if (isset($comp['example']['header_handle'][0])) {
                        $metaHandleOrUrl = $comp['example']['header_handle'][0];

                        // Meta sometimes returns the CDN URL inside header_handle
                        if (strpos($metaHandleOrUrl, 'http') === 0) {
                            $header_url = $metaHandleOrUrl;
                        } elseif ($existing) {
                            // Fallback to local
                            $exData = json_decode($existing['header_text'], true);
                            if (!empty($exData['header_url'])) {
                                $header_url = $exData['header_url'];
                            }
                        }
                    } elseif (isset($comp['example']['header_url'][0])) {
                        $header_url = $comp['example']['header_url'][0];
                    }
                }
                if ($comp['type'] == 'BUTTONS' && isset($comp['buttons'][0])) {
                    $button_text = $comp['buttons'][0]['text'] ?? '';
                    $button_url = $comp['buttons'][0]['url'] ?? '';
                }
            }

            // Build a JSON for buttons/header to save in db
            $extraData = [
                'header_type' => $header_type,
                'header_url' => $header_url,
                'button_text' => $button_text,
                'button_url' => $button_url
            ];

            $data = [
                'template_name' => $tpl['name'],
                'language' => $tpl['language'],
                'category' => $tpl['category'],
                'status' => $tpl['status'],
                'body_text' => $body,
                'footer_text' => $footer,
                'header_text' => json_encode($extraData),
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
        $filePath = ROOTPATH . 'public/uploads/whatsapp/' . $newName;
        $file->move(ROOTPATH . 'public/uploads/whatsapp', $newName);

        $token = env('WHATSAPP_API_TOKEN');
        $version = env('WHATSAPP_VERSION', 'v22.0');

        // 1. Get APP ID dynamically
        $ch = curl_init("https://graph.facebook.com/{$version}/debug_token?input_token={$token}&access_token={$token}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $appId = $res['data']['app_id'] ?? null;
        if (!$appId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Could not determine Facebook App ID']);
        }

        $filesize = filesize($filePath);
        $mimeType = mime_content_type($filePath);

        // 2. Initialize Resumable Upload API
        $urlUpload = "https://graph.facebook.com/{$version}/{$appId}/uploads?file_length={$filesize}&file_type={$mimeType}&access_token={$token}";
        $ch2 = curl_init($urlUpload);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $res2 = json_decode(curl_exec($ch2), true);
        curl_close($ch2);

        $uploadSessionId = $res2['id'] ?? null;
        if (!$uploadSessionId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to start upload session']);
        }

        // 3. Upload File bytes
        $urlUploadData = "https://graph.facebook.com/{$version}/{$uploadSessionId}";
        $ch3 = curl_init($urlUploadData);
        curl_setopt($ch3, CURLOPT_POST, true);
        curl_setopt($ch3, CURLOPT_HTTPHEADER, [
            "Authorization: OAuth {$token}",
            "file_offset: 0"
        ]);
        curl_setopt($ch3, CURLOPT_POSTFIELDS, file_get_contents($filePath));
        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        $res3 = json_decode(curl_exec($ch3), true);
        curl_close($ch3);

        if (isset($res3['h'])) {
            $mediaLink = base_url('uploads/whatsapp/' . $newName);
            return $this->response->setJSON(['status' => 'success', 'handle' => $res3['h'], 'url' => $mediaLink]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to obtain header handle from Meta']);
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
            $handle = $this->request->getPost('header_handle');
            $components[] = [
                'type' => 'HEADER',
                'format' => 'IMAGE',
                'example' => [
                    'header_handle' => [$handle]
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

            // Manually save the header_preview to DB so we can render it since Meta doesn't return public URLs for handles
            $previewUrl = $this->request->getPost('header_preview');
            if ($headerType == 'IMAGE' && $previewUrl) {
                $model = new \App\Models\TemplateModel();
                $tpl = $model->where('template_name', $name)->first();
                if ($tpl) {
                    $json = json_decode($tpl['header_text'], true) ?: [];
                    $json['header_url'] = $previewUrl;
                    $model->update($tpl['id'], ['header_text' => json_encode($json)]);
                }
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Template created successfully']);
        } else {
            $apiErrorMsg = $resData['error']['message'] ?? 'Meta API Error';
            $apiDetails = $resData['error']['error_user_msg'] ?? $resData['error']['error_user_title'] ?? '';

            $fullMessage = $apiDetails ? "$apiErrorMsg - $apiDetails" : $apiErrorMsg;

            return $this->response->setJSON(['status' => 'error', 'message' => $fullMessage, 'payload' => $payload]);
        }
    }
    public function edit()
    {
        $token = env('WHATSAPP_API_TOKEN');
        $version = env('WHATSAPP_VERSION', 'v22.0');

        $templateId = $this->request->getPost('meta_template_id');
        $category = $this->request->getPost('category');

        if (!$templateId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Template ID is missing for edit operation']);
        }

        $components = [];

        // HEADER
        $headerType = $this->request->getPost('header_type');
        if ($headerType == 'IMAGE') {
            $handle = $this->request->getPost('header_handle');
            // If they didn't upload a new image, handle might be empty locally. Meta doesn't allow omitting the example if format is IMAGE and editing. 
            // If header_handle is empty, we must use header_url pointing to the existing preview or notify user. Let's pass what's given.
            if ($handle) {
                $components[] = [
                    'type' => 'HEADER',
                    'format' => 'IMAGE',
                    'example' => [
                        'header_handle' => [$handle]
                    ]
                ];
            } else {
                // Try to use existing url
                $previewUrl = $this->request->getPost('header_preview');
                if ($previewUrl) {
                    $components[] = [
                        'type' => 'HEADER',
                        'format' => 'IMAGE',
                        'example' => [
                            'header_url' => [$previewUrl]
                        ]
                    ];
                } else {
                    $components[] = [
                        'type' => 'HEADER',
                        'format' => 'IMAGE'
                    ];
                }
            }
        }

        // BODY
        $bodyText = $this->request->getPost('body');
        $bodyExamples = $this->request->getPost('body_examples');

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
            'category' => $category,
            'components' => $components
        ];

        $url = "https://graph.facebook.com/{$version}/{$templateId}";

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

            // Retain/update local header\_url
            $previewUrl = $this->request->getPost('header_preview');
            if ($headerType == 'IMAGE' && $previewUrl) {
                $model = new \App\Models\TemplateModel();
                $tpl = $model->where('meta_template_id', $templateId)->first();
                if ($tpl) {
                    $json = json_decode($tpl['header_text'], true) ?: [];
                    $json['header_url'] = $previewUrl;
                    $model->update($tpl['id'], ['header_text' => json_encode($json)]);
                }
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Template edited successfully']);
        } else {
            $apiErrorMsg = $resData['error']['message'] ?? 'Meta API Error';
            $apiDetails = $resData['error']['error_user_msg'] ?? $resData['error']['error_user_title'] ?? '';
            $fullMessage = $apiDetails ? "$apiErrorMsg - $apiDetails" : $apiErrorMsg;
            return $this->response->setJSON(['status' => 'error', 'message' => $fullMessage, 'payload' => $payload]);
        }
    }

    public function getApproved()
    {
        $model = new \App\Models\TemplateModel();
        $templates = $model->where('status', 'APPROVED')->findAll();
        return $this->response->setJSON($templates);
    }
}
