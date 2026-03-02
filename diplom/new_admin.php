<?php
require_once 'config.php';

// Создаем администратора для Luxury Jewelry
$simple_password = 'AdminLuxury123!';
$hashed_password = password_hash($simple_password, PASSWORD_DEFAULT);

try {
    // Проверяем, существует ли уже админ
    $stmt = $pdo->prepare("SELECT id FROM luxury_users WHERE email_luxury = ?");
    $stmt->execute(['admin@luxury-jewelry.ru']);
    
    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO luxury_users 
            (username_luxury, email_luxury, password_luxury, is_admin_luxury, can_edit_design_luxury) 
            VALUES (?, ?, ?, TRUE, TRUE)");
        
        $stmt->execute([
            'Admin',
            'admin@luxury-jewelry.ru',
            $hashed_password
        ]);
        
        echo "НОВЫЙ АДМИН LUXURY СОЗДАН!<br>";
        echo "Логин: admin@luxury-jewelry.ru<br>";
        echo "Пароль: AdminLuxury123!<br>";
        echo "УДАЛИТЕ ЭТОТ ФАЙЛ ПОСЛЕ СОЗДАНИЯ!";
    } else {
        echo "Администратор уже существует!";
    }
    
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>