<?php
require_once 'config.php';

// Проверка администратора
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin_luxury']) {
    header('Location: login.php');
    exit();
}

// Проверяем права на редактирование оформления
$can_edit_design = false;
try {
    $stmt = $pdo->prepare("SELECT can_edit_design_luxury FROM luxury_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $can_edit_design = $user['can_edit_design_luxury'] ?? false;
} catch (PDOException $e) {
    // Ошибка при проверке прав
}

// Обработка редактирования изделия
$editing_product = null;
if (isset($_GET['edit_product'])) {
    $product_id = intval($_GET['edit_product']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM luxury_jewelry WHERE id = ?");
        $stmt->execute([$product_id]);
        $editing_product = $stmt->fetch();
    } catch (PDOException $e) {
        $error_message = "Ошибка при загрузке изделия: " . $e->getMessage();
    }
}

// Обработка редактирования заказа
$editing_order = null;
if (isset($_GET['edit_order'])) {
    $order_id = intval($_GET['edit_order']);
    try {
        $stmt = $pdo->prepare("SELECT o.*, u.username_luxury 
                              FROM luxury_orders o 
                              LEFT JOIN luxury_users u ON o.user_id_luxury = u.id 
                              WHERE o.id = ?");
        $stmt->execute([$order_id]);
        $editing_order = $stmt->fetch();
        
        if ($editing_order) {
            // Получаем изделия в заказе
            $stmt = $pdo->prepare("SELECT * FROM luxury_order_items WHERE order_id_luxury = ?");
            $stmt->execute([$order_id]);
            $order_items = $editing_order['items'] = $stmt->fetchAll();
            
            // Получаем историю статусов
            $stmt = $pdo->prepare("SELECT h.*, u.username_luxury as changed_by_name 
                                  FROM luxury_order_history h
                                  LEFT JOIN luxury_users u ON h.changed_by_luxury = u.id
                                  WHERE order_id_luxury = ? 
                                  ORDER BY changed_at_luxury DESC");
            $stmt->execute([$order_id]);
            $editing_order['history'] = $stmt->fetchAll();
        }
    } catch (PDOException $e) {
        $error_message = "Ошибка при загрузке заказа: " . $e->getMessage();
    }
}

// Обработка POST запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Добавление ювелирного изделия
    if (isset($_POST['add_product'])) {
        $name = trim($_POST['name_luxury']);
        $description = trim($_POST['description_luxury']);
        $price = floatval($_POST['price_luxury']);
        $category_id = intval($_POST['category_id_luxury']);
        $stock = intval($_POST['stock_luxury']);
        $material = trim($_POST['material_luxury']); // золото, серебро, платина
        $gemstones = trim($_POST['gemstones_luxury']); // камни
        $weight = floatval($_POST['weight_luxury'] ?? 0); // вес в граммах
        $image = trim($_POST['image_luxury']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO luxury_jewelry (name_luxury, description_luxury, price_luxury, category_id_luxury, stock_luxury, material_luxury, gemstones_luxury, weight_luxury, image_luxury) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $category_id, $stock, $material, $gemstones, $weight, $image]);
            $success_message = "Ювелирное изделие успешно добавлено!";
        } catch (PDOException $e) {
            $error_message = "Ошибка при добавлении изделия: " . $e->getMessage();
        }
    }
    
    // 2. Обновление изделия
    if (isset($_POST['update_product'])) {
        $product_id = intval($_POST['product_id']);
        $name = trim($_POST['name_luxury']);
        $description = trim($_POST['description_luxury']);
        $price = floatval($_POST['price_luxury']);
        $category_id = intval($_POST['category_id_luxury']);
        $stock = intval($_POST['stock_luxury']);
        $material = trim($_POST['material_luxury']);
        $gemstones = trim($_POST['gemstones_luxury']);
        $weight = floatval($_POST['weight_luxury'] ?? 0);
        $image = trim($_POST['image_luxury']);
        
        try {
            $stmt = $pdo->prepare("UPDATE luxury_jewelry SET name_luxury = ?, description_luxury = ?, price_luxury = ?, category_id_luxury = ?, stock_luxury = ?, material_luxury = ?, gemstones_luxury = ?, weight_luxury = ?, image_luxury = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $category_id, $stock, $material, $gemstones, $weight, $image, $product_id]);
            $success_message = "Изделие успешно обновлено!";
            $editing_product = null;
        } catch (PDOException $e) {
            $error_message = "Ошибка при обновлении изделия: " . $e->getMessage();
        }
    }
    
    // 3. Добавление категории
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name_luxury']);
        $description = trim($_POST['description_luxury']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO luxury_categories (name_luxury, description_luxury) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
            $success_message = "Категория успешно добавлена!";
        } catch (PDOException $e) {
            $error_message = "Ошибка при добавлении категории: " . $e->getMessage();
        }
    }
    
    // 4. Удаление изделия
    if (isset($_POST['delete_product'])) {
        $product_id = intval($_POST['product_id']);
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("DELETE FROM luxury_cart WHERE product_id_luxury = ?");
            $stmt->execute([$product_id]);
            
            $stmt = $pdo->prepare("DELETE FROM luxury_order_items WHERE product_id_luxury = ?");
            $stmt->execute([$product_id]);
            
            $stmt = $pdo->prepare("DELETE FROM luxury_jewelry WHERE id = ?");
            $stmt->execute([$product_id]);
            
            $pdo->commit();
            
            $success_message = "Изделие успешно удалено!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Ошибка при удалении изделия: " . $e->getMessage();
        }
    }
    
    // 5. Удаление категории
    if (isset($_POST['delete_category'])) {
        $category_id = intval($_POST['category_id']);
        
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM luxury_jewelry WHERE category_id_luxury = ?");
            $stmt->execute([$category_id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $error_message = "Невозможно удалить категорию, в ней есть изделия!";
            } else {
                $stmt = $pdo->prepare("DELETE FROM luxury_categories WHERE id = ?");
                $stmt->execute([$category_id]);
                $success_message = "Категория успешно удалена!";
            }
        } catch (PDOException $e) {
            $error_message = "Ошибка при удалении категории: " . $e->getMessage();
        }
    }
    
    // 6. Обновление статуса заказа
    if (isset($_POST['update_order_status'])) {
        $order_id = intval($_POST['order_id']);
        $new_status = trim($_POST['status']);
        $status_comment = trim($_POST['status_comment']);
        
        try {
            $stmt = $pdo->prepare("UPDATE luxury_orders SET status_luxury = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            
            $stmt = $pdo->prepare("INSERT INTO luxury_order_history (
                order_id_luxury,
                status_luxury,
                comment_luxury,
                changed_by_luxury
            ) VALUES (?, ?, ?, ?)");
            
            $stmt->execute([$order_id, $new_status, $status_comment, $_SESSION['user_id']]);
            
            $success_message = "Статус заказа успешно обновлен!";
            
            $stmt = $pdo->prepare("SELECT o.*, u.username_luxury 
                                  FROM luxury_orders o 
                                  LEFT JOIN luxury_users u ON o.user_id_luxury = u.id 
                                  WHERE o.id = ?");
            $stmt->execute([$order_id]);
            $editing_order = $stmt->fetch();
            
        } catch (PDOException $e) {
            $error_message = "Ошибка при обновлении статуса: " . $e->getMessage();
        }
    }
    
    // 7. Обновление информации о заказе
    if (isset($_POST['update_order_info'])) {
        $order_id = intval($_POST['order_id']);
        $customer_name = trim($_POST['customer_name']);
        $customer_phone = trim($_POST['customer_phone']);
        $customer_email = trim($_POST['customer_email']);
        $shipping_address = trim($_POST['shipping_address']);
        $notes = trim($_POST['notes']);
        
        try {
            $stmt = $pdo->prepare("UPDATE luxury_orders 
                                  SET customer_name_luxury = ?, 
                                      customer_phone_luxury = ?, 
                                      customer_email_luxury = ?, 
                                      shipping_address_luxury = ?, 
                                      notes_luxury = ? 
                                  WHERE id = ?");
            $stmt->execute([$customer_name, $customer_phone, $customer_email, $shipping_address, $notes, $order_id]);
            
            $success_message = "Информация о заказе успешно обновлена!";
            
            $stmt = $pdo->prepare("SELECT o.*, u.username_luxury 
                                  FROM luxury_orders o 
                                  LEFT JOIN luxury_users u ON o.user_id_luxury = u.id 
                                  WHERE o.id = ?");
            $stmt->execute([$order_id]);
            $editing_order = $stmt->fetch();
            
        } catch (PDOException $e) {
            $error_message = "Ошибка при обновлении заказа: " . $e->getMessage();
        }
    }
    
    // 8. Обновление оформления
    if ($can_edit_design && isset($_POST['update_design'])) {
        try {
            foreach ($_POST['design'] as $key => $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $stmt = $pdo->prepare("INSERT INTO luxury_site_settings (setting_key_luxury, setting_value_luxury) 
                                          VALUES (?, ?) 
                                          ON DUPLICATE KEY UPDATE setting_value_luxury = ?");
                    $stmt->execute([$key, $value, $value]);
                }
            }
            $success_message = "Оформление успешно обновлено!";
        } catch (PDOException $e) {
            $error_message = "Ошибка при обновлении оформления: " . $e->getMessage();
        }
    }
}

// Получаем данные для админки
try {
    // Ювелирные изделия
    $stmt = $pdo->query("SELECT p.*, c.name_luxury as category_name FROM luxury_jewelry p LEFT JOIN luxury_categories c ON p.category_id_luxury = c.id ORDER BY p.created_at_luxury DESC");
    $products = $stmt->fetchAll();
    
    // Категории
    $stmt = $pdo->query("SELECT * FROM luxury_categories ORDER BY name_luxury");
    $categories = $stmt->fetchAll();
    
    // Пользователи
    $stmt = $pdo->query("SELECT * FROM luxury_users ORDER BY registration_date_luxury DESC");
    $users = $stmt->fetchAll();
    
    // Заказы
    $stmt = $pdo->query("SELECT o.*, u.username_luxury 
                        FROM luxury_orders o 
                        LEFT JOIN luxury_users u ON o.user_id_luxury = u.id 
                        ORDER BY o.created_at_luxury DESC");
    $orders = $stmt->fetchAll();
    
    // Настройки оформления
    $stmt = $pdo->query("SELECT setting_key_luxury, setting_value_luxury FROM luxury_site_settings");
    $settings_data = $stmt->fetchAll();
    $design_settings = [];
    foreach ($settings_data as $setting) {
        $design_settings[$setting['setting_key_luxury']] = $setting['setting_value_luxury'];
    }
    
    // Статистика
    $total_products = count($products);
    $total_categories = count($categories);
    $total_users = count($users);
    $total_orders = count($orders);
    $admin_users = count(array_filter($users, fn($u) => $u['is_admin_luxury']));
    $pending_orders = count(array_filter($orders, fn($o) => $o['status_luxury'] == 'pending'));
    $completed_orders = count(array_filter($orders, fn($o) => $o['status_luxury'] == 'delivered'));
    
    // Подсчет общей выручки
    $total_revenue = 0;
    foreach ($orders as $order) {
        if ($order['status_luxury'] != 'cancelled') {
            $total_revenue += $order['total_amount_luxury'];
        }
    }
    
} catch (PDOException $e) {
    die("Ошибка при получении данных: " . $e->getMessage());
}

// Статусы заказов
$status_colors = [
    'pending' => '#ffc107',
    'processing' => '#17a2b8',
    'shipped' => '#6f42c1',
    'delivered' => '#28a745',
    'cancelled' => '#dc3545'
];

$status_labels = [
    'pending' => 'Ожидает',
    'processing' => 'В обработке',
    'shipped' => 'Отправлен',
    'delivered' => 'Доставлен',
    'cancelled' => 'Отменен'
];

$active_tab = 'orders';
if (isset($_GET['tab'])) {
    $active_tab = $_GET['tab'];
} elseif ($editing_product) {
    $active_tab = 'edit-product';
} elseif ($editing_order) {
    $active_tab = 'edit-order';
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 1400px; margin: 40px auto; padding: 0 20px;">
    <h1 style="color: <?php echo getSetting('primary_color', '#8B4513'); ?>; margin-bottom: 30px;">Luxury Jewelry - Панель администратора</h1>
    
    <!-- Уведомления -->
    <?php if(isset($success_message)): ?>
        <div style="background-color: <?php echo getSetting('success_color', '#d4edda'); ?>; 
                   border: 1px solid <?php echo getSetting('success_color', '#c3e6cb'); ?>; 
                   color: <?php echo getSetting('primary_color', '#155724'); ?>; 
                   padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: 500;">
            ✅ <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div style="background-color: <?php echo getSetting('error_color', '#f8d7da'); ?>; 
                   border: 1px solid <?php echo getSetting('error_color', '#f5c6cb'); ?>; 
                   color: <?php echo getSetting('error_color', '#721c24'); ?>; 
                   padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: 500;">
            ❌ <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Статистика -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background-color: <?php echo getSetting('primary_color', '#8B4513'); ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h3 style="color: #333; margin: 0 0 5px 0; font-size: 16px;">Заказы</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #17a2b8;"><?php echo $total_orders; ?></div>
                    <div style="font-size: 12px; color: #666;">Ожидают: <?php echo $pending_orders; ?></div>
                </div>
            </div>
        </div>
        
        <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background-color: <?php echo getSetting('accent_color', '#D4AF37'); ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </div>
                <div>
                    <h3 style="color: #333; margin: 0 0 5px 0; font-size: 16px;">Изделия</h3>
                    <div style="font-size: 24px; font-weight: bold; color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;"><?php echo $total_products; ?></div>
                    <div style="font-size: 12px; color: #666;">Категорий: <?php echo $total_categories; ?></div>
                </div>
            </div>
        </div>
        
        <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background-color: <?php echo getSetting('secondary_color', '#A0522D'); ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h3 style="color: #333; margin: 0 0 5px 0; font-size: 16px;">Клиенты</h3>
                    <div style="font-size: 24px; font-weight: bold; color: <?php echo getSetting('secondary_color', '#A0522D'); ?>;"><?php echo $total_users; ?></div>
                    <div style="font-size: 12px; color: #666;">VIP: <?php echo count(array_filter($users, fn($u) => $u['is_vip_luxury'])); ?></div>
                </div>
            </div>
        </div>
        
        <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center; gap: 15px;">
                <div style="width: 50px; height: 50px; background-color: #9c27b0; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                <div>
                    <h3 style="color: #333; margin: 0 0 5px 0; font-size: 16px;">Выручка</h3>
                    <div style="font-size: 24px; font-weight: bold; color: #9c27b0;"><?php echo number_format($total_revenue, 0, ',', ' '); ?> ₽</div>
                    <div style="font-size: 12px; color: #666;">Высокий чек: <?php echo count(array_filter($orders, fn($o) => $o['total_amount_luxury'] > 50000)); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Навигация -->
    <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <div style="display: flex; flex-wrap: wrap; gap: 5px;">
            <a href="?tab=orders" 
               style="padding: 10px 20px; background-color: <?php echo $active_tab == 'orders' ? getSetting('button_color', '#D4AF37') : '#f5f5f5'; ?>; 
                      color: <?php echo $active_tab == 'orders' ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 5px; font-weight: <?php echo $active_tab == 'orders' ? 'bold' : 'normal'; ?>; white-space: nowrap;">
                Заказы
            </a>
            <a href="?tab=products" 
               style="padding: 10px 20px; background-color: <?php echo $active_tab == 'products' ? getSetting('button_color', '#D4AF37') : '#f5f5f5'; ?>; 
                      color: <?php echo $active_tab == 'products' ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 5px; font-weight: <?php echo $active_tab == 'products' ? 'bold' : 'normal'; ?>; white-space: nowrap;">
                Изделия
            </a>
            <a href="?tab=categories" 
               style="padding: 10px 20px; background-color: <?php echo $active_tab == 'categories' ? getSetting('button_color', '#D4AF37') : '#f5f5f5'; ?>; 
                      color: <?php echo $active_tab == 'categories' ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 5px; font-weight: <?php echo $active_tab == 'categories' ? 'bold' : 'normal'; ?>; white-space: nowrap;">
                Категории
            </a>
            <a href="?tab=users" 
               style="padding: 10px 20px; background-color: <?php echo $active_tab == 'users' ? getSetting('button_color', '#D4AF37') : '#f5f5f5'; ?>; 
                      color: <?php echo $active_tab == 'users' ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 5px; font-weight: <?php echo $active_tab == 'users' ? 'bold' : 'normal'; ?>; white-space: nowrap;">
                Клиенты
            </a>
            <a href="?tab=add-product" 
               style="padding: 10px 20px; background-color: <?php echo $active_tab == 'add-product' || $active_tab == 'edit-product' ? getSetting('button_color', '#D4AF37') : '#f5f5f5'; ?>; 
                      color: <?php echo $active_tab == 'add-product' || $active_tab == 'edit-product' ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 5px; font-weight: <?php echo $active_tab == 'add-product' || $active_tab == 'edit-product' ? 'bold' : 'normal'; ?>; white-space: nowrap;">
                <?php echo $editing_product ? 'Редактировать изделие' : 'Добавить изделие'; ?>
            </a>
            <a href="?tab=add-category" 
               style="padding: 10px 20px; background-color: <?php echo $active_tab == 'add-category' ? getSetting('button_color', '#D4AF37') : '#f5f5f5'; ?>; 
                      color: <?php echo $active_tab == 'add-category' ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 5px; font-weight: <?php echo $active_tab == 'add-category' ? 'bold' : 'normal'; ?>; white-space: nowrap;">
                Добавить категорию
            </a>
            <?php if($can_edit_design): ?>
            <a href="?tab=design" 
               style="padding: 10px 20px; background-color: <?php echo $active_tab == 'design' ? getSetting('button_color', '#D4AF37') : '#f5f5f5'; ?>; 
                      color: <?php echo $active_tab == 'design' ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 5px; font-weight: <?php echo $active_tab == 'design' ? 'bold' : 'normal'; ?>; white-space: nowrap;">
                Оформление
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Контент -->
    <div id="tab-content">
        
        <!-- Вкладка Заказы -->
        <?php if($active_tab == 'orders' || $active_tab == 'edit-order'): ?>
            <?php if($editing_order): ?>
    <!-- Редактирование заказа -->
    <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #333; margin: 0;">
                Заказ <?php echo htmlspecialchars($editing_order['order_number_luxury']); ?>
            </h2>
            <a href="?tab=orders" 
               style="padding: 8px 15px; background-color: #f5f5f5; color: #666; text-decoration: none; border-radius: 5px;">
                ← Назад к заказам
            </a>
        </div>
        
        <!-- Форма редактирования заказа -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Левая часть - основная информация -->
            <div>
                <!-- Форма изменения статуса -->
                <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="color: #333; margin-bottom: 15px;">Изменение статуса</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="order_id" value="<?php echo $editing_order['id']; ?>">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Статус заказа</label>
                                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                    <option value="pending" <?php echo $editing_order['status_luxury'] == 'pending' ? 'selected' : ''; ?>>Ожидает обработки</option>
                                    <option value="processing" <?php echo $editing_order['status_luxury'] == 'processing' ? 'selected' : ''; ?>>В обработке</option>
                                    <option value="shipped" <?php echo $editing_order['status_luxury'] == 'shipped' ? 'selected' : ''; ?>>Отправлен</option>
                                    <option value="delivered" <?php echo $editing_order['status_luxury'] == 'delivered' ? 'selected' : ''; ?>>Доставлен</option>
                                    <option value="cancelled" <?php echo $editing_order['status_luxury'] == 'cancelled' ? 'selected' : ''; ?>>Отменен</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Комментарий</label>
                                <input type="text" name="status_comment" placeholder="Причина изменения" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                        </div>
                        <button type="submit" name="update_order_status" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Обновить статус
                        </button>
                    </form>
                </div>
                
                <!-- Форма редактирования информации -->
                <div style="background: #f9f9f9; padding: 20px; border-radius: 10px;">
                    <h3 style="color: #333; margin-bottom: 15px;">Информация о заказе</h3>
                    <form method="POST" action="">
                        <input type="hidden" name="order_id" value="<?php echo $editing_order['id']; ?>">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Имя клиента</label>
                                <input type="text" name="customer_name" value="<?php echo htmlspecialchars($editing_order['customer_name_luxury']); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Телефон</label>
                                <input type="text" name="customer_phone" value="<?php echo htmlspecialchars($editing_order['customer_phone_luxury']); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Email</label>
                                <input type="email" name="customer_email" value="<?php echo htmlspecialchars($editing_order['customer_email_luxury']); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 500;">Адрес доставки</label>
                                <input type="text" name="shipping_address" value="<?php echo htmlspecialchars($editing_order['shipping_address_luxury']); ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Комментарий клиента</label>
                            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><?php echo htmlspecialchars($editing_order['notes_luxury']); ?></textarea>
                        </div>
                        <button type="submit" name="update_order_info" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                            Сохранить изменения
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Правая часть - информация о заказе -->
            <div>
                <div style="background: #f9f9f9; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                    <h3 style="color: #333; margin-bottom: 15px;">Детали заказа</h3>
                    <div style="margin-bottom: 10px;">
                        <strong>Номер заказа:</strong> <?php echo htmlspecialchars($editing_order['order_number_luxury']); ?>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong>Дата создания:</strong> <?php echo date('d.m.Y H:i', strtotime($editing_order['created_at_luxury'])); ?>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong>Статус оплаты:</strong> 
                        <span style="padding: 3px 8px; border-radius: 4px; background-color: 
                            <?php echo $editing_order['payment_status_luxury'] == 'paid' ? '#28a745' : 
                                   ($editing_order['payment_status_luxury'] == 'pending' ? '#ffc107' : '#dc3545'); ?>; 
                            color: white;">
                            <?php echo $editing_order['payment_status_luxury']; ?>
                        </span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong>Способ оплаты:</strong> <?php echo htmlspecialchars($editing_order['payment_method_luxury']); ?>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong>Сумма заказа:</strong> 
                        <span style="font-weight: bold; color: #D4AF37;">
                            <?php echo number_format($editing_order['total_amount_luxury'], 0, ',', ' '); ?> ₽
                        </span>
                    </div>
                </div>
                
                <!-- Товары в заказе -->
                <div style="background: #f9f9f9; padding: 20px; border-radius: 10px;">
                    <h3 style="color: #333; margin-bottom: 15px;">Товары в заказе</h3>
                    <?php if(isset($order_items) && !empty($order_items)): ?>
                        <?php foreach($order_items as $item): ?>
                            <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                                <strong><?php echo htmlspecialchars($item['product_name_luxury']); ?></strong><br>
                                <small>Цена: <?php echo number_format($item['product_price_luxury'], 0, ',', ' '); ?> ₽</small> × 
                                <small>Кол-во: <?php echo $item['quantity_luxury']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Нет данных о товарах</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- История статусов -->
        <?php if(isset($editing_order['history']) && !empty($editing_order['history'])): ?>
        <div style="margin-top: 30px; background: #f9f9f9; padding: 20px; border-radius: 10px;">
            <h3 style="color: #333; margin-bottom: 15px;">История статусов</h3>
            <div style="max-height: 200px; overflow-y: auto;">
                <?php foreach($editing_order['history'] as $history): ?>
                    <div style="margin-bottom: 10px; padding: 10px; background: white; border-radius: 5px;">
                        <strong><?php echo $status_labels[$history['status_luxury']] ?? $history['status_luxury']; ?></strong><br>
                        <small>Дата: <?php echo date('d.m.Y H:i', strtotime($history['changed_at_luxury'])); ?></small><br>
                        <?php if($history['comment_luxury']): ?>
                            <small>Комментарий: <?php echo htmlspecialchars($history['comment_luxury']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
<?php else: ?>
                <!-- Список заказов -->
                <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="color: #333; margin: 0;">Управление заказами</h2>
                        <div style="color: #666; font-size: 14px;">
                            Всего: <?php echo $total_orders; ?> заказов
                        </div>
                    </div>
                    
                    <!-- Таблица заказов с роскошным дизайном -->
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, <?php echo getSetting('primary_color', '#8B4513'); ?>, <?php echo getSetting('secondary_color', '#A0522D'); ?>); color: white;">
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">№ Заказа</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Клиент</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Дата</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Сумма</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Статус</th>
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px;">
                                        <strong><?php echo htmlspecialchars($order['order_number_luxury']); ?></strong>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php echo htmlspecialchars($order['customer_name_luxury']); ?><br>
                                        <small style="color: #666;"><?php echo htmlspecialchars($order['username_luxury']); ?></small>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php echo date('d.m.Y', strtotime($order['created_at_luxury'])); ?>
                                    </td>
                                    <td style="padding: 12px; font-weight: bold; color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;">
                                        <?php echo number_format($order['total_amount_luxury'], 0, ',', ' '); ?> ₽
                                    </td>
                                    <td style="padding: 12px;">
                                        <span style="background-color: <?php echo $status_colors[$order['status_luxury']] ?? '#666'; ?>; color: white; padding: 5px 10px; border-radius: 12px; font-size: 12px;">
                                            <?php echo htmlspecialchars($status_labels[$order['status_luxury']] ?? $order['status_luxury']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <div style="display: flex; gap: 5px;">
                                            <a href="order.php?id=<?php echo $order['id']; ?>" target="_blank"
                                               style="padding: 5px 10px; background-color: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; text-decoration: none;">
                                                Просмотр
                                            </a>
                                            <a href="?tab=orders&edit_order=<?php echo $order['id']; ?>" 
                                               style="padding: 5px 10px; background-color: <?php echo getSetting('button_color', '#D4AF37'); ?>; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; text-decoration: none;">
                                                Управление
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Вкладка Изделия -->
        <?php if($active_tab == 'products'): ?>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: #333; margin: 0;">Коллекция ювелирных изделий</h2>
                    <a href="?tab=add-product" 
                       style="padding: 8px 15px; background-color: <?php echo getSetting('button_color', '#D4AF37'); ?>; color: white; text-decoration: none; border-radius: 5px;">
                        + Добавить изделие
                    </a>
                </div>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, <?php echo getSetting('primary_color', '#8B4513'); ?>, <?php echo getSetting('secondary_color', '#A0522D'); ?>); color: white;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Название</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Категория</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Материал</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Цена</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Остаток</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><?php echo $product['id']; ?></td>
                                <td style="padding: 12px;">
                                    <strong><?php echo htmlspecialchars($product['name_luxury']); ?></strong><br>
                                    <small style="color: #666;"><?php echo substr(htmlspecialchars($product['description_luxury']), 0, 50); ?>...</small>
                                </td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?></td>
                                <td style="padding: 12px;">
                                    <span style="background-color: #e6f7ff; color: #0066cc; padding: 3px 8px; border-radius: 12px; font-size: 12px;">
                                        <?php echo htmlspecialchars($product['material_luxury'] ?? 'Не указан'); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; font-weight: bold; color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;">
                                    <?php echo number_format($product['price_luxury'], 0, ',', ' '); ?> ₽
                                </td>
                                <td style="padding: 12px;">
                                    <span style="color: <?php echo ($product['stock_luxury'] > 0) ? getSetting('success_color', '#28a745') : getSetting('error_color', '#dc3545'); ?>; font-weight: bold;">
                                        <?php echo $product['stock_luxury']; ?> шт.
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    <div style="display: flex; gap: 5px;">
                                        <a href="?edit_product=<?php echo $product['id']; ?>" 
                                           style="padding: 5px 10px; background-color: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; text-decoration: none;">
                                            Редакт.
                                        </a>
                                        <form method="POST" onsubmit="return confirm('Удалить ювелирное изделие?')" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" name="delete_product" 
                                                    style="padding: 5px 10px; background-color: <?php echo getSetting('error_color', '#dc3545'); ?>; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
                                                Удалить
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Вкладка Категории -->
        <?php if($active_tab == 'categories'): ?>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: #333; margin: 0;">Категории ювелирных изделий</h2>
                    <a href="?tab=add-category" 
                       style="padding: 8px 15px; background-color: <?php echo getSetting('button_color', '#D4AF37'); ?>; color: white; text-decoration: none; border-radius: 5px;">
                        + Добавить категорию
                    </a>
                </div>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, <?php echo getSetting('primary_color', '#8B4513'); ?>, <?php echo getSetting('secondary_color', '#A0522D'); ?>); color: white;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Название</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Описание</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Дата создания</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><?php echo $category['id']; ?></td>
                                <td style="padding: 12px;"><strong><?php echo htmlspecialchars($category['name_luxury']); ?></strong></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($category['description_luxury']); ?></td>
                                <td style="padding: 12px;"><?php echo date('d.m.Y', strtotime($category['created_at_luxury'])); ?></td>
                                <td style="padding: 12px;">
                                    <form method="POST" onsubmit="return confirm('Удалить категорию?')" style="display: inline;">
                                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" name="delete_category" 
                                                style="padding: 5px 10px; background-color: <?php echo getSetting('error_color', '#dc3545'); ?>; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
                                            Удалить
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Вкладка Клиенты -->
        <?php if($active_tab == 'users'): ?>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <h2 style="color: #333; margin-bottom: 20px;">Клиенты Luxury Jewelry</h2>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, <?php echo getSetting('primary_color', '#8B4513'); ?>, <?php echo getSetting('secondary_color', '#A0522D'); ?>); color: white;">
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Имя</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Email</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Телефон</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Дата регистрации</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Статус</th>
                                <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Всего заказов</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): 
                                // Получаем количество заказов клиента
                                $stmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM luxury_orders WHERE user_id_luxury = ?");
                                $stmt->execute([$user['id']]);
                                $order_count = $stmt->fetch()['order_count'];
                            ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><?php echo $user['id']; ?></td>
                                <td style="padding: 12px;">
                                    <?php echo htmlspecialchars($user['username_luxury']); ?>
                                    <?php if($user['is_vip_luxury']): ?>
                                        <span style="background-color: <?php echo getSetting('accent_color', '#D4AF37'); ?>; color: white; padding: 2px 6px; border-radius: 10px; font-size: 10px; margin-left: 5px;">VIP</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($user['email_luxury']); ?></td>
                                <td style="padding: 12px;"><?php echo htmlspecialchars($user['phone_luxury'] ?: 'Не указан'); ?></td>
                                <td style="padding: 12px;"><?php echo date('d.m.Y', strtotime($user['registration_date_luxury'])); ?></td>
                                <td style="padding: 12px;">
                                    <?php if($user['is_admin_luxury']): ?>
                                        <span style="background-color: <?php echo getSetting('primary_color', '#8B4513'); ?>; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px;">Админ</span>
                                        <?php if($user['can_edit_design_luxury']): ?>
                                            <span style="background-color: #6f42c1; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; margin-left: 5px;">Дизайн</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="background-color: <?php echo getSetting('secondary_color', '#A0522D'); ?>; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px;">Клиент</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <span style="font-weight: bold; color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;">
                                        <?php echo $order_count; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Вкладка Добавить/Редактировать изделие -->
        <?php if($active_tab == 'add-product' || $active_tab == 'edit-product'): ?>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: #333; margin: 0;">
                        <?php echo $editing_product ? 'Редактирование ювелирного изделия' : 'Добавление нового изделия'; ?>
                    </h2>
                    <a href="?tab=products" 
                       style="padding: 8px 15px; background-color: #f5f5f5; color: #666; text-decoration: none; border-radius: 5px;">
                        ← Назад к изделиям
                    </a>
                </div>
                
                <form method="POST" action="">
                    <?php if($editing_product): ?>
                        <input type="hidden" name="product_id" value="<?php echo $editing_product['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Название изделия *</label>
                            <input type="text" name="name_luxury" required 
                                   value="<?php echo $editing_product ? htmlspecialchars($editing_product['name_luxury']) : ''; ?>"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Категория *</label>
                            <select name="category_id_luxury" required 
                                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Выберите категорию</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"
                                            <?php echo ($editing_product && $editing_product['category_id_luxury'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name_luxury']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Описание изделия</label>
                        <textarea name="description_luxury" rows="4" 
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><?php echo $editing_product ? htmlspecialchars($editing_product['description_luxury']) : ''; ?></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Цена *</label>
                            <input type="number" name="price_luxury" required step="0.01" min="0"
                                   value="<?php echo $editing_product ? $editing_product['price_luxury'] : ''; ?>"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Количество на складе *</label>
                            <input type="number" name="stock_luxury" required min="0"
                                   value="<?php echo $editing_product ? $editing_product['stock_luxury'] : ''; ?>"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Материал</label>
                            <select name="material_luxury" 
                                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <option value="">Выберите материал</option>
                                <option value="золото" <?php echo ($editing_product && $editing_product['material_luxury'] == 'золото') ? 'selected' : ''; ?>>Золото</option>
                                <option value="серебро" <?php echo ($editing_product && $editing_product['material_luxury'] == 'серебро') ? 'selected' : ''; ?>>Серебро</option>
                                <option value="платина" <?php echo ($editing_product && $editing_product['material_luxury'] == 'платина') ? 'selected' : ''; ?>>Платина</option>
                                <option value="белое золото" <?php echo ($editing_product && $editing_product['material_luxury'] == 'белое золото') ? 'selected' : ''; ?>>Белое золото</option>
                                <option value="розовое золото" <?php echo ($editing_product && $editing_product['material_luxury'] == 'розовое золото') ? 'selected' : ''; ?>>Розовое золото</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Драгоценные камни</label>
                            <input type="text" name="gemstones_luxury" 
                                   value="<?php echo $editing_product ? htmlspecialchars($editing_product['gemstones_luxury']) : ''; ?>"
                                   placeholder="Бриллианты, рубины и т.д."
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Вес (грамм)</label>
                            <input type="number" name="weight_luxury" step="0.01" min="0"
                                   value="<?php echo $editing_product ? $editing_product['weight_luxury'] : ''; ?>"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">URL изображения</label>
                        <input type="text" name="image_luxury" 
                               value="<?php echo $editing_product ? htmlspecialchars($editing_product['image_luxury']) : ''; ?>"
                               placeholder="https://example.com/image.jpg"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <?php if($editing_product): ?>
                            <button type="submit" name="update_product" 
                                    style="padding: 12px 30px; background-color: <?php echo getSetting('accent_color', '#D4AF37'); ?>; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                                Обновить изделие
                            </button>
                        <?php else: ?>
                            <button type="submit" name="add_product" 
                                    style="padding: 12px 30px; background-color: <?php echo getSetting('button_color', '#D4AF37'); ?>; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                                Добавить изделие
                            </button>
                        <?php endif; ?>
                        
                        <a href="?tab=products" 
                           style="padding: 12px 30px; background-color: #f5f5f5; color: #666; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Вкладка Добавить категорию -->
        <?php if($active_tab == 'add-category'): ?>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="color: #333; margin: 0;">Добавление новой категории</h2>
                    <a href="?tab=categories" 
                       style="padding: 8px 15px; background-color: #f5f5f5; color: #666; text-decoration: none; border-radius: 5px;">
                        ← Назад к категориям
                    </a>
                </div>
                
                <form method="POST" action="">
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Название категории *</label>
                        <input type="text" name="name_luxury" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                               placeholder="Например: Обручальные кольца, Серьги, Подвески">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; color: #555; font-weight: bold;">Описание категории</label>
                        <textarea name="description_luxury" rows="4" 
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                                  placeholder="Описание категории ювелирных изделий"></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="add_category" 
                                style="padding: 12px 30px; background-color: <?php echo getSetting('button_color', '#D4AF37'); ?>; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                            Добавить категорию
                        </button>
                        
                        <a href="?tab=categories" 
                           style="padding: 12px 30px; background-color: #f5f5f5; color: #666; text-decoration: none; border-radius: 5px; font-size: 16px; display: inline-block;">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
        <!-- Вкладка Оформление -->
        <?php if($can_edit_design && $active_tab == 'design'): ?>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <h2 style="color: #333; margin-bottom: 20px;">Настройка оформления Luxury Jewelry</h2>
                
                <form method="POST" action="">
                    <div style="margin-bottom: 30px;">
                        <h3 style="color: #555; margin-bottom: 15px; font-size: 18px;">Цветовая схема</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                            <?php 
                            $color_groups = [
                                'Роскошные тона' => [
                                    'primary_color' => 'Основной цвет (коричневый/бордовый)',
                                    'secondary_color' => 'Вторичный цвет (темный шоколад)',
                                    'accent_color' => 'Акцентный цвет (золотой)',
                                    'button_color' => 'Цвет кнопок (золотой)'
                                ],
                                'Фон и текст' => [
                                    'background_color' => 'Цвет фона (кремовый/бежевый)',
                                    'card_color' => 'Цвет карточек (белый/слоновая кость)',
                                    'text_color' => 'Цвет текста (темный шоколад)'
                                ],
                                'Состояния' => [
                                    'success_color' => 'Цвет успеха (изумрудный)',
                                    'error_color' => 'Цвет ошибок (рубиновый)'
                                ]
                            ];
                            
                            foreach ($color_groups as $group_name => $colors): 
                            ?>
                            <div style="margin-bottom: 20px;">
                                <h4 style="color: #555; margin-bottom: 10px; font-size: 16px;"><?php echo $group_name; ?></h4>
                                <div style="display: grid; gap: 10px;">
                                    <?php foreach ($colors as $key => $label): ?>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 30px; height: 30px; border-radius: 4px; border: 1px solid #ddd; background-color: <?php echo $design_settings[$key] ?? '#ffffff'; ?>;"></div>
                                        <div style="flex: 1;">
                                            <label style="display: block; margin-bottom: 3px; color: #555; font-size: 14px;"><?php echo $label; ?></label>
                                            <input type="color" name="design[<?php echo $key; ?>]" value="<?php echo $design_settings[$key] ?? '#ffffff'; ?>"
                                                   style="width: 100%; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div style="background-color: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
                        <h3 style="color: #555; margin-bottom: 10px;">Предварительный просмотр</h3>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <div style="padding: 15px; border-radius: 5px; background: linear-gradient(135deg, <?php echo $design_settings['primary_color'] ?? '#8B4513'; ?> 0%, <?php echo $design_settings['secondary_color'] ?? '#A0522D'; ?> 100%); color: white;">
                                Шапка Luxury Jewelry
                            </div>
                            <div style="padding: 15px; border-radius: 5px; background-color: <?php echo $design_settings['accent_color'] ?? '#D4AF37'; ?>; color: white;">
                                Золотой акцент
                            </div>
                            <div style="padding: 15px; border-radius: 5px; background-color: <?php echo $design_settings['button_color'] ?? '#D4AF37'; ?>; color: white;">
                                Кнопка покупки
                            </div>
                            <div style="padding: 15px; border-radius: 5px; background-color: <?php echo $design_settings['error_color'] ?? '#dc3545'; ?>; color: white;">
                                Сообщение об ошибке
                            </div>
                            <div style="padding: 15px; border-radius: 5px; background-color: <?php echo $design_settings['success_color'] ?? '#28a745'; ?>; color: white;">
                                Подтверждение заказа
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="update_design" 
                                style="padding: 12px 30px; background-color: <?php echo getSetting('button_color', '#D4AF37'); ?>; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                            Сохранить оформление
                        </button>
                        
                        <button type="button" onclick="resetDesign()"
                                style="padding: 12px 30px; background-color: #f5f5f5; color: #666; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                            Сбросить к стандартным
                        </button>
                    </div>
                </form>
            </div>
            
            <script>
            function resetDesign() {
                if (confirm('Сбросить все настройки оформления к стандартным значениям?')) {
                    const defaultDesign = {
                        'primary_color': '#8B4513',
                        'secondary_color': '#A0522D',
                        'accent_color': '#D4AF37',
                        'text_color': '#333333',
                        'background_color': '#f5f5f5',
                        'card_color': '#ffffff',
                        'button_color': '#D4AF37',
                        'error_color': '#dc3545',
                        'success_color': '#28a745'
                    };
                    
                    for (const [key, value] of Object.entries(defaultDesign)) {
                        const input = document.querySelector(`input[name="design[${key}]"]`);
                        if (input) {
                            input.value = value;
                        }
                    }
                    
                    alert('Оформление сброшено к стандартным значениям. Нажмите "Сохранить оформление" для применения.');
                }
            }
            </script>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>