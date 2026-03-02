<?php
session_start();

// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'c96841ax_1');
define('DB_NAME', 'c96841ax_1');
define('DB_PASS', 'xyiHA*mEqkX0');

// Подключение к базе данных
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получаем настройки оформления из БД
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key_luxury, setting_value_luxury FROM luxury_site_settings");
    $settings_data = $stmt->fetchAll();
    
    // Преобразуем в ассоциативный массив
    foreach ($settings_data as $setting) {
        $settings[$setting['setting_key_luxury']] = $setting['setting_value_luxury'];
    }
} catch (PDOException $e) {
    // Если таблица не существует, используем настройки по умолчанию
    $settings = [
        'primary_color' => '#8B4513',
        'secondary_color' => '#A0522D',
        'accent_color' => '#D4AF37',
        'text_color' => '#333333',
        'background_color' => '#f5f5f5',
        'card_color' => '#ffffff',
        'button_color' => '#D4AF37',
        'error_color' => '#dc3545',
        'success_color' => '#28a745',
        'font_family' => "'Helvetica Neue', Arial, sans-serif"
    ];
}

// Константы для администратора
define('ADMIN_USERNAME', 'admin_luxury');
define('ADMIN_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); // Хеш пароля AdminLuxury123!

// Настройки магазина
define('SHOP_NAME', 'Luxury Jewelry');
define('SHOP_CURRENCY', '₽');
define('FREE_SHIPPING_THRESHOLD', 10000);
define('SHIPPING_COST', 1000);
define('VIP_THRESHOLD', 100000);
define('VIP_DISCOUNT_PERCENT', 10);

// Функция для получения значения настройки
function getSetting($key, $default = '') {
    global $settings;
    return isset($settings[$key]) ? $settings[$key] : $default;
}

// Функция для форматирования цены
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' ' . SHOP_CURRENCY;
}

// Функция для получения текущего пользователя
function getCurrentUser() {
    global $pdo;
    
    if (isset($_SESSION['user_id'])) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM luxury_users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    return null;
}

// Функция для проверки VIP статуса
function isUserVIP($user_id = null) {
    global $pdo;
    
    if (!$user_id && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    if ($user_id) {
        try {
            $stmt = $pdo->prepare("SELECT is_vip_luxury, total_spent_luxury FROM luxury_users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            if ($user) {
                return $user['is_vip_luxury'] || $user['total_spent_luxury'] >= VIP_THRESHOLD;
            }
        } catch (PDOException $e) {
            return false;
        }
    }
    
    return false;
}

// Функция для получения количества товаров в корзине
function getCartCount() {
    global $pdo;
    
    if (isset($_SESSION['user_id'])) {
        try {
            $stmt = $pdo->prepare("SELECT SUM(quantity_luxury) as total FROM luxury_cart WHERE user_id_luxury = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    return 0;
}

// Автоматическое создание необходимых таблиц при первом запуске
function checkAndCreateTables() {
    global $pdo;
    
    $tables = [
        "CREATE TABLE IF NOT EXISTS luxury_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username_luxury VARCHAR(100) NOT NULL,
            email_luxury VARCHAR(100) UNIQUE NOT NULL,
            password_luxury VARCHAR(255) NOT NULL,
            phone_luxury VARCHAR(20),
            address_luxury TEXT,
            birthday_luxury DATE,
            is_admin_luxury BOOLEAN DEFAULT FALSE,
            is_vip_luxury BOOLEAN DEFAULT FALSE,
            can_edit_design_luxury BOOLEAN DEFAULT FALSE,
            order_count_luxury INT DEFAULT 0,
            total_spent_luxury DECIMAL(10,2) DEFAULT 0,
            registration_date_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name_luxury VARCHAR(100) NOT NULL,
            description_luxury TEXT,
            created_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_jewelry (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name_luxury VARCHAR(200) NOT NULL,
            description_luxury TEXT,
            price_luxury DECIMAL(10,2) NOT NULL,
            category_id_luxury INT,
            stock_luxury INT DEFAULT 0,
            material_luxury VARCHAR(50),
            gemstones_luxury VARCHAR(200),
            weight_luxury DECIMAL(5,2),
            image_luxury VARCHAR(500),
            created_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id_luxury) REFERENCES luxury_categories(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_cart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id_luxury INT NOT NULL,
            product_id_luxury INT NOT NULL,
            quantity_luxury INT DEFAULT 1,
            added_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id_luxury) REFERENCES luxury_users(id),
            FOREIGN KEY (product_id_luxury) REFERENCES luxury_jewelry(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id_luxury INT NOT NULL,
            order_number_luxury VARCHAR(50) UNIQUE NOT NULL,
            total_amount_luxury DECIMAL(10,2) NOT NULL,
            subtotal_luxury DECIMAL(10,2),
            discount_amount_luxury DECIMAL(10,2) DEFAULT 0,
            shipping_cost_luxury DECIMAL(10,2) DEFAULT 0,
            promo_discount_luxury DECIMAL(10,2) DEFAULT 0,
            gift_wrapping_luxury BOOLEAN DEFAULT FALSE,
            shipping_address_luxury TEXT NOT NULL,
            delivery_method_luxury VARCHAR(50),
            customer_name_luxury VARCHAR(100) NOT NULL,
            customer_phone_luxury VARCHAR(20) NOT NULL,
            customer_email_luxury VARCHAR(100) NOT NULL,
            payment_method_luxury VARCHAR(50) NOT NULL,
            status_luxury VARCHAR(50) DEFAULT 'pending',
            notes_luxury TEXT,
            card_data_luxury JSON,
            created_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id_luxury) REFERENCES luxury_users(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id_luxury INT NOT NULL,
            product_id_luxury INT,
            product_name_luxury VARCHAR(200) NOT NULL,
            product_price_luxury DECIMAL(10,2) NOT NULL,
            product_material_luxury VARCHAR(50),
            quantity_luxury INT NOT NULL,
            subtotal_luxury DECIMAL(10,2) NOT NULL,
            FOREIGN KEY (order_id_luxury) REFERENCES luxury_orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id_luxury) REFERENCES luxury_jewelry(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_order_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id_luxury INT NOT NULL,
            status_luxury VARCHAR(50) NOT NULL,
            comment_luxury TEXT,
            changed_by_luxury INT,
            changed_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id_luxury) REFERENCES luxury_orders(id) ON DELETE CASCADE,
            FOREIGN KEY (changed_by_luxury) REFERENCES luxury_users(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key_luxury VARCHAR(100) UNIQUE NOT NULL,
            setting_value_luxury VARCHAR(255) NOT NULL,
            updated_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS luxury_wishlist (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id_luxury INT NOT NULL,
            product_id_luxury INT NOT NULL,
            added_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id_luxury) REFERENCES luxury_users(id),
            FOREIGN KEY (product_id_luxury) REFERENCES luxury_jewelry(id),
            UNIQUE KEY unique_wishlist (user_id_luxury, product_id_luxury)
        )"
    ];
    
    try {
        foreach ($tables as $tableSql) {
            $pdo->exec($tableSql);
        }
        
        // Вставляем настройки по умолчанию
        $defaultSettings = [
            ['primary_color', '#8B4513'],
            ['secondary_color', '#A0522D'],
            ['accent_color', '#D4AF37'],
            ['text_color', '#333333'],
            ['background_color', '#f5f5f5'],
            ['card_color', '#ffffff'],
            ['button_color', '#D4AF37'],
            ['error_color', '#dc3545'],
            ['success_color', '#28a745'],
            ['font_family', "'Helvetica Neue', Arial, sans-serif"],
            ['shop_name', 'Luxury Jewelry'],
            ['shop_email', 'info@luxury-jewelry.ru'],
            ['shop_phone', '+7 (999) 123-45-67'],
            ['shop_address', 'Москва, ул. Тверская, 10'],
            ['instagram_url', 'https://instagram.com/luxury_jewelry'],
            ['facebook_url', 'https://facebook.com/luxuryjewelry'],
            ['whatsapp_url', 'https://wa.me/79991234567']
        ];
        
        foreach ($defaultSettings as $setting) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO luxury_site_settings (setting_key_luxury, setting_value_luxury) VALUES (?, ?)");
            $stmt->execute($setting);
        }
        
        // Создаем администратора по умолчанию
        $adminCheck = $pdo->query("SELECT COUNT(*) as count FROM luxury_users WHERE is_admin_luxury = TRUE");
        if ($adminCheck->fetch()['count'] == 0) {
            $stmt = $pdo->prepare("INSERT INTO luxury_users (username_luxury, email_luxury, password_luxury, is_admin_luxury, can_edit_design_luxury) VALUES (?, ?, ?, TRUE, TRUE)");
            $stmt->execute(['Admin', 'admin@luxury-jewelry.ru', password_hash('AdminLuxury123!', PASSWORD_DEFAULT)]);
        }
        
    } catch (PDOException $e) {
        // Логируем ошибку, но не прерываем выполнение
        error_log("Ошибка при создании таблиц: " . $e->getMessage());
    }
}

// Вызываем функцию проверки таблиц при каждом запуске
checkAndCreateTables();

// Инициализируем корзину в сессии
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = getCartCount();
}
?>