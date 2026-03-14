CREATE TABLE IF NOT EXISTS whatsapp_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    template_name VARCHAR(255) NOT NULL,
    language VARCHAR(50) DEFAULT 'en_US',
    category VARCHAR(50) DEFAULT 'MARKETING',
    status VARCHAR(50) DEFAULT 'PENDING',
    body_text TEXT,
    header_text TEXT,
    footer_text TEXT,
    buttons TEXT,
    meta_template_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Standard MySQL Alter Table (Run these one by one if they don't exist)
ALTER TABLE whatsapp_logs ADD COLUMN message_id VARCHAR(255);
ALTER TABLE whatsapp_logs ADD COLUMN direction ENUM('outbound', 'inbound') DEFAULT 'outbound';
ALTER TABLE whatsapp_logs ADD COLUMN read_status VARCHAR(50) DEFAULT 'sent';

-- Webhook Debug Table (stores raw incoming webhook payloads for testing)
CREATE TABLE IF NOT EXISTS webhook_debug (
    id INT AUTO_INCREMENT PRIMARY KEY,
    raw_body LONGTEXT,
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
