<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class WhatsappWebhook extends BaseController
{
    /**
     * Meta / Facebook Webhook Verification (GET)
     */
    public function verify()
    {
        // Define your verify token in .env: WHATSAPP_WEBHOOK_VERIFY_TOKEN="nss_verify_secure"
        $verifyToken = env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', 'nss_verify_secure');
        
        $mode = $this->request->getGet('hub_mode');
        $token = $this->request->getGet('hub_verify_token');
        $challenge = $this->request->getGet('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return $this->response->setBody($challenge);
        }

        return $this->response->setStatusCode(403)->setBody('Verification failed');
    }

    /**
     * Meta Status / Messages Notification (POST)
     */
    public function receive()
    {
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setJSON(['status' => 'no_content']);
        }

        // 1. Handle MESSAGE STATUS UPDATES (sent, delivered, read, failed)
        if (isset($data['entry'][0]['changes'][0]['value']['statuses'][0])) {
            $statusObj = $data['entry'][0]['changes'][0]['value']['statuses'][0];
            $msgId = $statusObj['id']; // wamid.HBgM...
            $status = $statusObj['status']; // delivered, read, failed
            
            $logModel = new \App\Models\WhatsappLogModel();
            $logModel->where('message_id', $msgId)->set(['status' => $status])->update();
            log_message('info', "WhatsApp status for $msgId updated to: $status");
        }

        // 2. Handle INCOMING MESSAGES FROM CUSTOMERS
        if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            $messageObj = $data['entry'][0]['changes'][0]['value']['messages'][0];
            $fromNumber = $messageObj['from']; // 911234567890
            $msgId = $messageObj['id'];
            $type = $messageObj['type'] ?? 'text';

            $messageText = '';
            $attachmentName = null;
            $attachmentType = null;

            if ($type === 'text') {
                $messageText = $messageObj['text']['body'] ?? '';
            } elseif ($type === 'location') {
                 $loc = $messageObj['location'];
                 $messageText = "📍 Location: " . ($loc['name'] ?? '') . " (" . $loc['latitude'] . ", " . $loc['longitude'] . ")";
            } else {
                 // It's a Media item (image, video, audio, document)
                 $messageText = "📎 Sent a " . ucfirst($type);
                 if (isset($messageObj[$type]['id'])) {
                     $mediaId = $messageObj[$type]['id'];
                     $media = $this->downloadMetaMedia($mediaId);
                     if ($media) {
                         $attachmentName = json_encode([$media['name']]);
                         $attachmentType = json_encode([$media['type']]);
                     }
                 }
            }

            // Find customer by matching last 10 digits
            $cleanPhone = substr($fromNumber, -10);
            $customerModel = new \App\Models\CustomerModel();
            $customer = $customerModel->like('phone', $cleanPhone)->first();

            if ($customer) {
                $logModel = new \App\Models\WhatsappLogModel();
                $logModel->insert([
                    'customer_id' => $customer['id'],
                    'message'     => $messageText,
                    'attachment'  => $attachmentName,
                    'attachment_type' => $attachmentType,
                    'direction'   => 'inbound',
                    'message_id'  => $msgId,
                    'status'      => 'unread',
                    'sent_at'     => date('Y-m-d H:i:s')
                ]);
            }
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    /**
     * Download media from Meta and save to local uploads/whatsapp
     */
    private function downloadMetaMedia($mediaId)
    {
        $token = env('WHATSAPP_API_TOKEN');
        $version = env('WHATSAPP_VERSION', 'v21.0');

        if (!$token) return null;

        // Step 1: Get download URL from Media ID nodes
        $url = "https://graph.facebook.com/{$version}/{$mediaId}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $resData = json_decode($response, true);
        if (isset($resData['url'])) {
            $downloadUrl = $resData['url'];
            
            // Step 2: Download binary stream
            $ch = curl_init($downloadUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $token]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $fileData = curl_exec($ch);
            curl_close($ch);

            if ($fileData) {
                $ext = 'bin';
                if (isset($resData['mime_type'])) {
                    $mime = $resData['mime_type'];
                    if (strpos($mime, 'image/') === 0) $ext = 'jpg';
                    elseif (strpos($mime, 'video/') === 0) $ext = 'mp4';
                    elseif (strpos($mime, 'audio/') === 0) $ext = 'mp3';
                    elseif ($mime === 'application/pdf') $ext = 'pdf';
                }

                $filename = 'inc_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $dir = ROOTPATH . 'public/uploads/whatsapp/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);

                file_put_contents($dir . $filename, $fileData);
                return [
                    'name' => $filename,
                    'type' => $resData['mime_type'] ?? 'application/octet-stream'
                ];
            }
        }
        return null;
    }

    /**
     * Get unread incoming messages for global topbar notification counter
     */
    public function getUnreadMessages()
    {
        $db = \Config\Database::connect();
        
        // Fetch unread inbound messages joined with customers table for Display Name
        $query = $db->query("
            SELECT wl.*, c.name as customer_name 
            FROM whatsapp_logs wl
            JOIN customers c ON wl.customer_id = c.id
            WHERE wl.direction = 'inbound' AND wl.status = 'unread'
            ORDER BY wl.sent_at DESC
            LIMIT 10
        ");
        
        return $this->response->setJSON($query->getResultArray());
    }

    /**
     * Get single customer by ID
     */
    public function getCustomer($id)
    {
        $customerModel = new \App\Models\CustomerModel();
        $customer = $customerModel->find($id);
        
        if (!$customer) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'error', 'message' => 'Customer not found']);
        }

        return $this->response->setJSON($customer);
    }

    /**
     * Mark customer messages as read
     */
    public function markRead($id)
    {
        $logModel = new \App\Models\WhatsappLogModel();
        $logModel->where('customer_id', $id)
                 ->where('direction', 'inbound')
                 ->where('status', 'unread')
                 ->set(['status' => 'read'])
                 ->update();
                 
        return $this->response->setJSON(['status' => 'success']);
    }
}
