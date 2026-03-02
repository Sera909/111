<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $action = isset($_POST['action']) ? $_POST['action'] : 'toggle';
    
    try {
        // Проверяем, есть ли уже в избранном
        $stmt = $pdo->prepare("SELECT * FROM luxury_wishlist WHERE user_id_luxury = ? AND product_id_luxury = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $exists = $stmt->fetch();
        
        if ($action === 'add' || ($action === 'toggle' && !$exists)) {
            // Добавляем в избранное
            $stmt = $pdo->prepare("INSERT INTO luxury_wishlist (user_id_luxury, product_id_luxury, added_at_luxury) VALUES (?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            echo json_encode(['success' => true, 'action' => 'added']);
        } elseif ($action === 'remove' || ($action === 'toggle' && $exists)) {
            // Удаляем из избранного
            $stmt = $pdo->prepare("DELETE FROM luxury_wishlist WHERE user_id_luxury = ? AND product_id_luxury = ?");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
            echo json_encode(['success' => true, 'action' => 'removed']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>