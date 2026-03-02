<?php
require_once 'config.php';

// Проверка администратора
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin_luxury']) {
    header('Location: login.php');
    exit();
}

$message = '';

try {
    // Проверяем существование таблицы настроек
    $stmt = $pdo->query("SHOW TABLES LIKE 'luxury_site_settings'");
    $table_exists = $stmt->fetch();
    
    if (!$table_exists) {
        $message = "Таблица настроек не существует. Создайте её через SQL: <br><br>
        CREATE TABLE luxury_site_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key_luxury VARCHAR(100) NOT NULL UNIQUE,
            setting_value_luxury VARCHAR(255) NOT NULL,
            updated_at_luxury TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );";
    } else {
        // Проверяем наличие настроек
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM luxury_site_settings");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            // Добавляем настройки по умолчанию для Luxury Jewelry
            $default_settings = [
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
            
            foreach ($default_settings as $setting) {
                $stmt = $pdo->prepare("INSERT INTO luxury_site_settings (setting_key_luxury, setting_value_luxury) VALUES (?, ?)");
                $stmt->execute($setting);
            }
            
            $message = "Настройки оформления Luxury Jewelry успешно установлены!<br><br>";
            $message .= "Основной цвет: #8B4513 (коричневый)<br>";
            $message .= "Вторичный цвет: #A0522D (коричневый светлый)<br>";
            $message .= "Акцентный цвет: #D4AF37 (золотой)<br><br>";
            $message .= "Настройки можно изменить в админ-панели.";
        } else {
            $message = "Настройки оформления уже существуют в базе данных.";
        }
    }
} catch (PDOException $e) {
    $message = "Ошибка: " . $e->getMessage();
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 800px; margin: 60px auto; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(139, 69, 19, 0.1); border: 1px solid #e8e8e8;">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 48px; color: #D4AF37; margin-bottom: 15px;">💎</div>
        <h1 style="color: #8B4513; margin-bottom: 10px; font-weight: 300; letter-spacing: 1px;">Установка настроек Luxury Jewelry</h1>
        <p style="color: #666; font-size: 14px;">Настройки оформления и цветов сайта</p>
    </div>
    
    <div style="padding: 25px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 10px; margin-bottom: 30px; border: 1px solid #e8d9c5;">
        <div style="color: #8B4513; font-weight: 500; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-info-circle"></i> Результат установки
        </div>
        <div style="color: #333; line-height: 1.6; font-size: 15px;">
            <?php echo $message; ?>
        </div>
    </div>
    
    <!-- Превью цветовой схемы -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #8B4513; margin-bottom: 20px; font-weight: 400; font-size: 18px;">Превью цветовой схемы</h3>
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            <div style="background: #8B4513; color: white; padding: 20px; border-radius: 10px; text-align: center;">
                <div style="font-weight: 600; font-size: 14px;">Основной</div>
                <div style="font-size: 12px; opacity: 0.9;">#8B4513</div>
            </div>
            <div style="background: #A0522D; color: white; padding: 20px; border-radius: 10px; text-align: center;">
                <div style="font-weight: 600; font-size: 14px;">Вторичный</div>
                <div style="font-size: 12px; opacity: 0.9;">#A0522D</div>
            </div>
            <div style="background: #D4AF37; color: #333; padding: 20px; border-radius: 10px; text-align: center;">
                <div style="font-weight: 600; font-size: 14px;">Акцентный</div>
                <div style="font-size: 12px; opacity: 0.9;">#D4AF37</div>
            </div>
            <div style="background: #f5f5f5; color: #333; padding: 20px; border-radius: 10px; text-align: center; border: 1px solid #ddd;">
                <div style="font-weight: 600; font-size: 14px;">Фон</div>
                <div style="font-size: 12px; opacity: 0.9;">#f5f5f5</div>
            </div>
        </div>
    </div>
    
    <div style="display: flex; gap: 15px; margin-top: 30px; flex-wrap: wrap;">
        <a href="admin.php?tab=design" 
           style="padding: 14px 30px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s; display: inline-flex; align-items: center; gap: 10px;">
            <i class="fas fa-palette"></i> Настроить дизайн
        </a>
        <a href="admin.php" 
           style="padding: 14px 30px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s; border: 1px solid #e8d9c5; display: inline-flex; align-items: center; gap: 10px;">
            <i class="fas fa-cog"></i> В админ-панель
        </a>
        <a href="index.php" 
           style="padding: 14px 30px; background: white; color: #666; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s; border: 1px solid #ddd; display: inline-flex; align-items: center; gap: 10px;">
            <i class="fas fa-home"></i> На главную
        </a>
    </div>
    
    <!-- Предупреждение -->
    <?php if(strpos($message, 'Таблица настроек не существует') !== false): ?>
    <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-radius: 10px; border: 1px solid #ffd54f;">
        <div style="color: #8B4513; font-weight: 500; margin-bottom: 10px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-triangle"></i> Важно
        </div>
        <p style="color: #333; font-size: 14px; margin: 0; line-height: 1.5;">
            Таблица настроек автоматически создаётся при первом запуске сайта через config.php.
            Если вы видите это сообщение, возможно, функция автоматического создания таблиц отключена.
        </p>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Автоматическая перезагрузка настроек
    const message = document.querySelector('div[style*="Результат установки"]').textContent;
    if (message.includes('успешно установлены') || message.includes('уже существуют')) {
        // Обновляем стили страницы через 1 секунду
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
});
</script>

<?php include 'footer.php'; ?>