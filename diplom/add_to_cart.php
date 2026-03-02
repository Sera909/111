<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $action = $_POST['action'] ?? 'add';
    $quantity = intval($_POST['quantity'] ?? 1);
    
    try {
        if ($action === 'add') {
            // Проверяем существование ювелирного изделия
            $stmt = $pdo->prepare("SELECT * FROM luxury_jewelry WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Изделие не найдено']);
                exit();
            }
            
            if ($product['stock_luxury'] <= 0) {
                echo json_encode(['success' => false, 'message' => 'Изделие отсутствует на складе']);
                exit();
            }
            
            // Проверяем, есть ли изделие уже в корзине
            $stmt = $pdo->prepare("SELECT * FROM luxury_cart WHERE user_id_luxury = ? AND product_id_luxury = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                // Обновляем количество
                $new_quantity = $existing['quantity_luxury'] + $quantity;
                if ($new_quantity > $product['stock_luxury']) {
                    $new_quantity = $product['stock_luxury'];
                }
                
                $stmt = $pdo->prepare("UPDATE luxury_cart SET quantity_luxury = ? WHERE id = ?");
                $stmt->execute([$new_quantity, $existing['id']]);
            } else {
                // Добавляем новое изделие
                $stmt = $pdo->prepare("INSERT INTO luxury_cart (user_id_luxury, product_id_luxury, quantity_luxury) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
            }
        }
        
        // Получаем количество изделий в корзине
        $stmt = $pdo->prepare("SELECT SUM(quantity_luxury) as total FROM luxury_cart WHERE user_id_luxury = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();
        $cart_count = $result['total'] ?? 0;
        
        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
}
?>