<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\WhatsappLogModel;
use App\Models\CustomerModel;

class Webhook extends BaseController
{
    /**
     * Verify Webhook (GET request from Meta)
     */
    public function verify()
    {
        $verifyToken = env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', 'nss_verify_token');

        $mode      = $this->request->getGet('hub_mode');
        $token     = $this->request->getGet('hub_verify_token');
        $challenge = $this->request->getGet('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return $this->response->setStatusCode(200)->setBody($challenge);
        }

        return $this->response->setStatusCode(403)->setBody('Forbidden');
    }

    /**
     * Receive Webhook Notifications (POST request from Meta)
     */
    public function receive()
    {
        $body = $this->request->getBody();
        $data = json_decode($body, true);

        // ── STORE RAW BODY for debugging ──
        try {
            $db = \Config\Database::connect();
            $db->table('webhook_debug')->insert([
                'raw_body'    => $body ?: '(empty)',
                'received_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'webhook_debug insert failed: ' . $e->getMessage());
        }

        // Always return 200 to Meta immediately
        if (!$data) {
            return $this->response->setStatusCode(200)->setBody('OK');
        }

        // Log raw payload for debugging (optional, remove in production)
        log_message('info', 'WEBHOOK RECEIVED: ' . $body);

        $logModel      = new WhatsappLogModel();
        $customerModel = new CustomerModel();

        // Handle full Meta webhook format:
        // { "entry": [{ "changes": [{ "value": { "messages": [...] } }] }] }
        $entries = $data['entry'] ?? [];

        foreach ($entries as $entry) {
            $changes = $entry['changes'] ?? [];

            foreach ($changes as $change) {
                $field = $change['field'] ?? '';
                $value = $change['value'] ?? [];

                // ─────────────────────────────────────────────
                // INCOMING MESSAGES
                // ─────────────────────────────────────────────
                if ($field === 'messages' && !empty($value['messages'])) {
                    foreach ($value['messages'] as $msg) {
                        $from  = $msg['from'] ?? null;   // e.g. "916383455764"
                        $msgId = $msg['id']   ?? null;
                        $type  = $msg['type'] ?? 'text';

                        // Extract text body
                        $text = match ($type) {
                            'text'     => $msg['text']['body']             ?? '',
                            'image'    => '[Image received]',
                            'document' => '[Document received]',
                            'audio'    => '[Audio received]',
                            'video'    => '[Video received]',
                            'sticker'  => '[Sticker received]',
                            default    => '[Message received]'
                        };

                        if (!$from) continue;

                        // Find customer - try exact match first, then last 10 digits
                        $customer = $customerModel->where('phone', $from)->first();
                        if (!$customer) {
                            // Try matching last 10 digits (handles country code differences)
                            $last10 = substr($from, -10);
                            $customer = $customerModel
                                ->like('phone', $last10, 'before')
                                ->first();
                        }

                        if ($customer) {
                            $logModel->insert([
                                'customer_id' => $customer['id'],
                                'message'     => $text,
                                'direction'   => 'inbound',
                                'message_id'  => $msgId,
                                'status'      => 'received',
                                'sent_at'     => date('Y-m-d H:i:s'),
                            ]);
                        } else {
                            // Log unknown senders for reference
                            log_message('info', "WEBHOOK: No customer found for phone: {$from}");
                        }
                    }
                }

                // ─────────────────────────────────────────────
                // STATUS UPDATES (delivered, read, sent, failed)
                // ─────────────────────────────────────────────
                if (!empty($value['statuses'])) {
                    foreach ($value['statuses'] as $status) {
                        $msgId = $status['id']     ?? null;
                        $st    = $status['status'] ?? null;

                        if ($msgId && $st) {
                            $logModel
                                ->where('message_id', $msgId)
                                ->set(['read_status' => $st])
                                ->update();
                        }
                    }
                }
            }
        }

        return $this->response->setStatusCode(200)->setBody('OK');
    }
}
