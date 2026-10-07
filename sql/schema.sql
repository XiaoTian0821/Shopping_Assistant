CREATE DATABASE IF NOT EXISTS ai_shopping_assistant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ai_shopping_assistant;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(40) NOT NULL UNIQUE,
    barcode VARCHAR(64) NULL,
    name VARCHAR(120) NOT NULL,
    detect_keywords VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    description TEXT NULL,
    image_path VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FULLTEXT INDEX ft_name_keywords (name, detect_keywords)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (email, password, name) VALUES
('admin@example.com', '$2a$12$Owgxj.71xYkQPeuFmeJFuOtJNyGYHN5CL1E53JRHdtixs/j/aPOO6', 'Administrator'),
('demo@store.com', '$2a$12$F4w72d1TZNQvWqCURntM9.sJdxWRkjdFYKDLnqck0S.ll8i3fKyei', 'Demo User');

INSERT INTO settings (setting_key, setting_value) VALUES
('ai_provider', 'gemini'),
('gemini_api_url', 'https://generativelanguage.googleapis.com/v1beta'),
('gemini_api_key', ''),
('gemini_model', 'gemini-2.0-flash'),
('gemini_fallback_models', ''),
('agnes_api_url', 'https://api.agnes.ai/v1'),
('agnes_api_key', ''),
('agnes_model', 'agnes-2.5-flash'),
('agnes_fallback_models', '');

INSERT INTO products (sku, barcode, name, detect_keywords, price, description) VALUES
('SKU-001', '8857200535366', 'Thai Herbal Inhaler', 'thai inhaler herbal nasal sniff stick', 5.00, 'Classic Thai herbal inhaler with refreshing scent. Made from natural herbs including camphor, menthol, and eucalyptus.'),
('SKU-002', '8857200535373', 'Mint Inhaler', 'mint inhaler fresh breath', 3.50, 'Refreshing mint inhaler for quick freshness throughout the day.'),
('SKU-003', '8857200535380', 'Lavender Inhaler', 'lavender inhaler relax calming', 5.50, 'Calming lavender herbal inhaler for relaxation and stress relief.'),
('SKU-004', '8857200535397', 'Eucalyptus Inhaler', 'eucalyptus inhaler respiratory clear', 5.00, 'Eucalyptus herbal inhaler for nasal congestion and respiratory comfort.'),
('SKU-005', '8857200535403', 'Peppermint Inhaler', 'peppermint inhaler energy boost', 4.00, ' Energizing peppermint inhaler for focus and alertness.'),
('SKU-006', '8857200535410', 'Lemongrass Inhaler', 'lemongrass inhaler citrus fresh', 5.00, 'Fresh lemongrass herbal inhaler with uplifting citrus aroma.'),
('SKU-007', '8857200535427', 'Sleep Inhaler', 'sleep inhaler relax bedtime lavender', 6.00, 'Specialized sleep aid inhaler with lavender and chamomile blend.'),
('SKU-008', '8857200535434', 'Focus Inhaler', 'focus inhaler concentrate study', 5.50, 'Focus-enhancing herbal inhaler for studying and concentration.'),
('SKU-009', '8857200535441', 'Energy Inhaler', 'energy inhaler vitality active', 5.00, 'Energy-boosting herbal inhaler for active lifestyle.'),
('SKU-010', '8857200535458', 'Calm Inhaler', 'calm inhaler anxiety relief relax', 5.50, 'Calming herbal inhaler for anxiety relief and peace of mind.');
