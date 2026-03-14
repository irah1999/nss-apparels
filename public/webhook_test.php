<?php

/**
 * WEBHOOK TEST + DEBUG PAGE
 * Access: http://localhost:8080/webhook_test.php
 * REMOVE THIS FILE AFTER TESTING!
 */

// Simulate an inbound message (bypass webhook)
if ($_GET['simulate'] ?? false) {
    $from = $_GET['phone'] ?? '916383455764';
    $text = $_GET['msg'] ?? 'Hello from test!';

    // Direct DB insert
    $dsn = 'mysql:host=127.0.0.1;dbname=nss_apparels;charset=utf8mb4';
    try {
        // Load CI4 config
        define('FCPATH', __DIR__ . '/');
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    [$key, $value] = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim(trim($value), '"');
                }
            }
        }

        $host     = $_ENV['database.default.hostname'] ?? '127.0.0.1';
        $dbname   = $_ENV['database.default.database'] ?? 'nss_apparels';
        $user     = $_ENV['database.default.username'] ?? 'root';
        $password = $_ENV['database.default.password'] ?? '';

        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);

        // Find customer by phone
        $stmt = $pdo->prepare("SELECT id, name FROM customers WHERE phone = ?");
        $stmt->execute([$from]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer) {
            $ins = $pdo->prepare("INSERT INTO whatsapp_logs (customer_id, message, direction, status, sent_at) VALUES (?, ?, 'inbound', 'received', NOW())");
            $ins->execute([$customer['id'], $text]);
            echo json_encode(['status' => 'ok', 'message' => "Message inserted for customer: {$customer['name']}"]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "No customer found with phone: $from"]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Webhook Debug</title>
    <style>
        body {
            font-family: monospace;
            background: #111;
            color: #0f0;
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }

        h2 {
            color: #0ff;
        }

        .box {
            background: #222;
            border: 1px solid #333;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }

        input,
        button {
            padding: 8px 12px;
            margin: 5px;
            background: #333;
            color: #fff;
            border: 1px solid #555;
            border-radius: 4px;
        }

        button {
            background: #1a5;
            cursor: pointer;
        }

        .result {
            background: #181;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }

        .warn {
            color: #fa0;
        }

        a {
            color: #0af;
        }
    </style>
</head>

<body>
    <h2>🔧 WhatsApp Webhook Debug Tool</h2>

    <div class="box">
        <h3>1. Simulate Inbound Message (Direct DB Insert)</h3>
        <p class="warn">⚠️ This inserts a fake inbound message directly into DB to test chat display</p>
        <input type="text" id="phone" placeholder="Customer Phone (e.g. 916383455764)" value="916383455764" style="width:300px">
        <input type="text" id="msg" placeholder="Test message" value="Hello, this is a test reply!" style="width:300px">
        <br>
        <button onclick="simulate()">Simulate Customer Reply</button>
        <div class="result" id="simResult"></div>
    </div>

    <div class="box">
        <h3>2. Test Webhook POST (Simulate Meta POST)</h3>
        <button onclick="testWebhook()">Send Test POST to /webhook</button>
        <div class="result" id="webhookResult"></div>
    </div>

    <div class="box">
        <h3>3. ngrok Status</h3>
        <p>ngrok URL: <a href="https://unapparent-margarette-femininely.ngrok-free.dev" target="_blank">https://unapparent-margarette-femininely.ngrok-free.dev</a></p>
        <p>Webhook URL in Meta: <strong>https://unapparent-margarette-femininely.ngrok-free.dev/webhook</strong></p>
        <p>Verify Token: <strong>nss_verify_token</strong></p>
    </div>

    <div class="box">
        <h3>4. Meta Developer Portal Steps</h3>
        <ol>
            <li>Go to <a href="https://developers.facebook.com" target="_blank">developers.facebook.com</a></li>
            <li>Select your App → <strong>WhatsApp → Configuration</strong></li>
            <li>Under Webhook: Click <strong>Edit</strong></li>
            <li>Callback URL: <code>https://unapparent-margarette-femininely.ngrok-free.dev/webhook</code></li>
            <li>Verify Token: <code>nss_verify_token</code></li>
            <li>Click <strong>Verify and Save</strong></li>
            <li>Click <strong>Manage</strong> → Subscribe to: <code>messages</code></li>
        </ol>
    </div>

    <script>
        async function simulate() {
            const phone = document.getElementById('phone').value;
            const msg = document.getElementById('msg').value;
            const res = await fetch(`/webhook_test.php?simulate=1&phone=${encodeURIComponent(phone)}&msg=${encodeURIComponent(msg)}`);
            const data = await res.json();
            document.getElementById('simResult').innerHTML = JSON.stringify(data, null, 2);
        }

        async function testWebhook() {
            const payload = {
                object: "whatsapp_business_account",
                entry: [{
                    changes: [{
                        value: {
                            messages: [{
                                from: "916383455764",
                                id: "test_msg_" + Date.now(),
                                text: {
                                    body: "Test webhook message " + new Date().toLocaleTimeString()
                                },
                                type: "text"
                            }]
                        }
                    }]
                }]
            };

            const res = await fetch('/webhook', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            document.getElementById('webhookResult').innerHTML = `Status: ${res.status} ${res.statusText}`;
        }
    </script>
</body>

</html>