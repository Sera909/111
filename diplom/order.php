<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id <= 0) {
    header('Location: cabinet.php');
    exit();
}

try {
    // Получаем информацию о заказе
    $stmt = $pdo->prepare("SELECT o.*, u.username_luxury 
                          FROM luxury_orders o
                          LEFT JOIN luxury_users u ON o.user_id_luxury = u.id
                          WHERE o.id = ? AND (o.user_id_luxury = ? OR ? = 1)");
    $is_admin = isset($_SESSION['is_admin_luxury']) && $_SESSION['is_admin_luxury'] ? 1 : 0;
    $stmt->execute([$order_id, $_SESSION['user_id'], $is_admin]);
    $order = $stmt->fetch();
    
    if (!$order) {
        header('Location: cabinet.php');
        exit();
    }
    
    // Получаем товары в заказе
    $stmt = $pdo->prepare("SELECT * FROM luxury_order_items WHERE order_id_luxury = ?");
    $stmt->execute([$order_id]);
    $order_items = $stmt->fetchAll();
    
    // Получаем историю статусов
    $stmt = $pdo->prepare("SELECT h.*, u.username_luxury as changed_by_name 
                          FROM luxury_order_history h
                          LEFT JOIN luxury_users u ON h.changed_by_luxury = u.id
                          WHERE order_id_luxury = ? 
                          ORDER BY changed_at_luxury DESC");
    $stmt->execute([$order_id]);
    $order_history = $stmt->fetchAll();
    
} catch (PDOException $e) {
    die("Ошибка при получении данных: " . $e->getMessage());
}

// Обработка изменения статуса (только для админа)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['is_admin_luxury']) && $_SESSION['is_admin_luxury']) {
    if (isset($_POST['update_status'])) {
        $new_status = trim($_POST['status']);
        $status_comment = trim($_POST['status_comment']);
        
        try {
            // Обновляем статус заказа
            $stmt = $pdo->prepare("UPDATE luxury_orders SET status_luxury = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            
            // Добавляем запись в историю
            $stmt = $pdo->prepare("INSERT INTO luxury_order_history (
                order_id_luxury,
                status_luxury,
                comment_luxury,
                changed_by_luxury
            ) VALUES (?, ?, ?, ?)");
            
            $stmt->execute([$order_id, $new_status, $status_comment, $_SESSION['user_id']]);
            
            $success_message = "Статус заказа успешно обновлен!";
            
            // Перезагружаем данные заказа
            $stmt = $pdo->prepare("SELECT * FROM luxury_orders WHERE id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch();
            
        } catch (PDOException $e) {
            $error_message = "Ошибка при обновлении статуса: " . $e->getMessage();
        }
    }
}

// Статусы и их цвета
$status_colors = [
    'pending' => '#D4AF37',
    'processing' => '#2196f3',
    'shipped' => '#673ab7',
    'delivered' => '#4caf50',
    'cancelled' => '#f44336'
];

$status_labels = [
    'pending' => 'Ожидает обработки',
    'processing' => 'В обработке',
    'shipped' => 'Отправлен',
    'delivered' => 'Доставлен',
    'cancelled' => 'Отменен'
];

// Статусы оплаты
$payment_status_colors = [
    'pending' => '#D4AF37',
    'paid' => '#4caf50',
    'failed' => '#f44336',
    'refunded' => '#9c27b0'
];

$payment_status_labels = [
    'pending' => 'Ожидает оплаты',
    'paid' => 'Оплачено',
    'failed' => 'Ошибка оплаты',
    'refunded' => 'Возврат средств'
];
?>

<?php include 'header.php'; ?>

<div style="max-width: 1400px; margin: 0 auto; padding: 40px;">
    <!-- Хлебные крошки -->
    <div style="margin-bottom: 30px;">
        <a href="cabinet.php" style="color: <?php echo getSetting('primary_color', '#8B4513'); ?>; text-decoration: none; font-weight: 500;">Личный кабинет</a>
        <span style="color: #A0522D; margin: 0 10px;">›</span>
        <span style="color: #666;">Заказ <?php echo htmlspecialchars($order['order_number_luxury']); ?></span>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start;">
        <!-- Основная информация -->
        <div>
            <!-- Шапка заказа -->
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #e8e8e8;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
                    <div>
                        <h1 style="color: #333; margin: 0 0 10px 0; font-weight: 300; letter-spacing: 1px; font-size: 28px;">
                            Заказ <?php echo htmlspecialchars($order['order_number_luxury']); ?>
                        </h1>
                        <div style="color: #888; font-size: 14px; display: flex; align-items: center; gap: 20px;">
                            <span>Создан: <?php echo date('d.m.Y H:i', strtotime($order['created_at_luxury'])); ?></span>
                            <?php if ($order['updated_at_luxury'] != $order['created_at_luxury']): ?>
                                <span>| Обновлен: <?php echo date('d.m.Y H:i', strtotime($order['updated_at_luxury'])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                        <span style="background-color: <?php echo $status_colors[$order['status_luxury']] ?? '#666'; ?>; color: white; padding: 10px 25px; border-radius: 25px; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-box"></i> <?php echo htmlspecialchars($status_labels[$order['status_luxury']] ?? $order['status_luxury']); ?>
                        </span>
                        
                        <span style="background-color: <?php echo $payment_status_colors[$order['payment_status_luxury']] ?? '#666'; ?>; color: white; padding: 8px 20px; border-radius: 25px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-credit-card"></i> <?php echo htmlspecialchars($payment_status_labels[$order['payment_status_luxury']] ?? $order['payment_status_luxury']); ?>
                        </span>
                    </div>
                </div>
                
                <!-- Форма изменения статуса (только для админа) -->
                <?php if(isset($_SESSION['is_admin_luxury']) && $_SESSION['is_admin_luxury']): ?>
                    <form method="POST" action="" style="margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee;">
                        <h3 style="color: #333; margin-bottom: 20px; font-weight: 400; font-size: 18px;">Изменить статус заказа</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Новый статус</label>
                                <select name="status" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; color: #555;">
                                    <?php foreach ($status_labels as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo ($order['status_luxury'] == $value) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Комментарий</label>
                                <input type="text" name="status_comment" 
                                       placeholder="Причина изменения статуса"
                                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                            </div>
                        </div>
                        
                        <button type="submit" name="update_status" 
                                style="padding: 12px 30px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500; font-size: 14px; transition: all 0.3s;">
                            <i class="fas fa-sync-alt"></i> Обновить статус
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            
            <!-- Товары в заказе -->
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid #e8e8e8;">
                <h3 style="color: #333; margin-bottom: 25px; font-weight: 400; font-size: 20px; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-gem" style="color: #D4AF37;"></i> Изделия в заказе
                </h3>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <thead>
                            <tr style="background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);">
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #e8d9c5; color: #8B4513; font-weight: 500;">Изделие</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #e8d9c5; color: #8B4513; font-weight: 500;">Цена</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #e8d9c5; color: #8B4513; font-weight: 500;">Количество</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 2px solid #e8d9c5; color: #8B4513; font-weight: 500;">Сумма</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $index => $item): ?>
                            <tr style="border-bottom: 1px solid #f0f0f0; <?php echo $index % 2 == 0 ? 'background-color: #fcf9f5;' : ''; ?>">
                                <td style="padding: 20px 15px;">
                                    <strong style="color: #333; font-weight: 500;"><?php echo htmlspecialchars($item['product_name_luxury']); ?></strong>
                                    <?php if($item['product_material_luxury']): ?>
                                        <div style="color: #888; font-size: 13px; margin-top: 5px;">Материал: <?php echo htmlspecialchars($item['product_material_luxury']); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 20px 15px; color: #333; font-weight: 500;">
                                    <?php echo number_format($item['product_price_luxury'], 0, ',', ' '); ?> ₽
                                </td>
                                <td style="padding: 20px 15px;">
                                    <span style="background-color: #f0e6d6; color: #8B4513; padding: 6px 15px; border-radius: 15px; font-weight: 600; font-size: 14px;">
                                        <?php echo $item['quantity_luxury']; ?> шт.
                                    </span>
                                </td>
                                <td style="padding: 20px 15px; font-weight: 600; color: #D4AF37; font-size: 16px;">
                                    <?php echo number_format($item['subtotal_luxury'], 0, ',', ' '); ?> ₽
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);">
                                <td colspan="3" style="padding: 25px 15px; text-align: right; font-weight: 600; font-size: 16px; color: #8B4513;">Итого:</td>
                                <td style="padding: 25px 15px; font-weight: 700; font-size: 24px; color: #D4AF37;">
                                    <?php echo number_format($order['total_amount_luxury'], 0, ',', ' '); ?> ₽
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <!-- История статусов -->
            <?php if(!empty($order_history)): ?>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
                <h3 style="color: #333; margin-bottom: 25px; font-weight: 400; font-size: 20px; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-history" style="color: #D4AF37;"></i> История статусов
                </h3>
                
                <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                    <?php foreach ($order_history as $index => $history): ?>
                        <div style="padding: 20px; border-bottom: 1px solid #f0f0f0; margin-bottom: 15px; background-color: <?php echo $index % 2 == 0 ? '#fcf9f5' : 'white'; ?>; border-radius: 10px; border-left: 4px solid <?php echo $status_colors[$history['status_luxury']] ?? '#666'; ?>;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div style="font-weight: 600;">
                                    <span style="background-color: <?php echo $status_colors[$history['status_luxury']] ?? '#666'; ?>; color: white; padding: 6px 18px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-flag"></i> <?php echo htmlspecialchars($status_labels[$history['status_luxury']] ?? $history['status_luxury']); ?>
                                    </span>
                                </div>
                                <div style="color: #888; font-size: 13px; display: flex; align-items: center; gap: 6px;">
                                    <i class="far fa-clock"></i> <?php echo date('d.m.Y H:i', strtotime($history['changed_at_luxury'])); ?>
                                </div>
                            </div>
                            
                            <?php if(!empty($history['comment_luxury'])): ?>
                                <div style="color: #666; font-size: 14px; margin-top: 15px; padding: 12px; background-color: #fff8e1; border-radius: 8px; border-left: 3px solid #D4AF37;">
                                    <strong style="color: #8B4513;"><i class="fas fa-comment"></i> Комментарий:</strong> <?php echo htmlspecialchars($history['comment_luxury']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($history['changed_by_name'])): ?>
                                <div style="color: #666; font-size: 14px; margin-top: 12px;">
                                    <strong style="color: #8B4513;"><i class="fas fa-user-edit"></i> Изменил:</strong> <?php echo htmlspecialchars($history['changed_by_name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Боковая панель -->
        <div>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 30px; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8; position: sticky; top: 30px;">
                <h3 style="color: #333; margin-bottom: 25px; font-weight: 400; font-size: 20px; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-info-circle" style="color: #D4AF37;"></i> Информация о заказе
                </h3>
                
                <!-- Информация о клиенте -->
                <div style="margin-bottom: 30px;">
                    <h4 style="color: #555; margin-bottom: 15px; font-size: 16px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-user-circle" style="color: #A0522D;"></i> Клиент
                    </h4>
                    <div style="background-color: #fcf9f5; padding: 20px; border-radius: 10px; border: 1px solid #f0e6d6;">
                        <p style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-user" style="color: #A0522D; width: 16px;"></i>
                            <strong style="color: #8B4513; min-width: 80px;">Имя:</strong> 
                            <span style="color: #333;"><?php echo htmlspecialchars($order['customer_name_luxury']); ?></span>
                        </p>
                        <p style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-phone" style="color: #A0522D; width: 16px;"></i>
                            <strong style="color: #8B4513; min-width: 80px;">Телефон:</strong> 
                            <span style="color: #333;"><?php echo htmlspecialchars($order['customer_phone_luxury']); ?></span>
                        </p>
                        <p style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-envelope" style="color: #A0522D; width: 16px;"></i>
                            <strong style="color: #8B4513; min-width: 80px;">Email:</strong> 
                            <span style="color: #333;"><?php echo htmlspecialchars($order['customer_email_luxury']); ?></span>
                        </p>
                        <?php if(isset($_SESSION['is_admin_luxury']) && $_SESSION['is_admin_luxury']): ?>
                            <p style="margin: 15px 0 0 0; padding-top: 15px; border-top: 1px dashed #e8d9c5; font-size: 14px; color: #666; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-user-tag" style="color: #A0522D; width: 16px;"></i>
                                <strong style="color: #8B4513; min-width: 80px;">Аккаунт:</strong> 
                                <span style="color: #333;"><?php echo htmlspecialchars($order['username_luxury']); ?></span>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Доставка -->
                <div style="margin-bottom: 30px;">
                    <h4 style="color: #555; margin-bottom: 15px; font-size: 16px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-truck" style="color: #A0522D;"></i> Доставка
                    </h4>
                    <div style="background-color: #fcf9f5; padding: 20px; border-radius: 10px; border: 1px solid #f0e6d6;">
                        <p style="margin: 0; white-space: pre-line; line-height: 1.6; color: #333; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fas fa-map-marker-alt" style="color: #A0522D; margin-top: 3px;"></i>
                            <span><?php echo htmlspecialchars($order['shipping_address_luxury']); ?></span>
                        </p>
                        <?php if($order['delivery_method_luxury']): ?>
                            <p style="margin: 15px 0 0 0; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-shipping-fast" style="color: #A0522D;"></i>
                                <strong style="color: #8B4513;">Способ:</strong> 
                                <span style="color: #333;"><?php echo htmlspecialchars($order['delivery_method_luxury']); ?></span>
                            </p>
                        <?php endif; ?>
                        <p style="margin: 10px 0 0 0; color: #888; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-money-bill-wave" style="color: #A0522D;"></i>
                            <span>Стоимость доставки: <?php echo number_format($order['shipping_cost_luxury'], 0, ',', ' '); ?> ₽</span>
                        </p>
                    </div>
                </div>
                
                <!-- Оплата -->
                <div style="margin-bottom: 30px;">
                    <h4 style="color: #555; margin-bottom: 15px; font-size: 16px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-credit-card" style="color: #A0522D;"></i> Оплата
                    </h4>
                    <div style="background-color: #fcf9f5; padding: 20px; border-radius: 10px; border: 1px solid #f0e6d6;">
                        <?php
                        $payment_methods = [
                            'cash' => 'Наличными при получении',
                            'card' => 'Банковской картой онлайн',
                            'card_courier' => 'Картой курьеру'
                        ];
                        ?>
                        <p style="margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-credit-card" style="color: #A0522D; width: 16px;"></i>
                            <strong style="color: #8B4513; min-width: 80px;">Способ:</strong> 
                            <span style="color: #333;"><?php echo htmlspecialchars($payment_methods[$order['payment_method_luxury']] ?? $order['payment_method_luxury']); ?></span>
                        </p>
                        <p style="margin: 0; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-circle" style="color: <?php echo $payment_status_colors[$order['payment_status_luxury']] ?? '#666'; ?>; width: 16px;"></i>
                            <strong style="color: #8B4513; min-width: 80px;">Статус:</strong> 
                            <span style="background-color: <?php echo $payment_status_colors[$order['payment_status_luxury']] ?? '#666'; ?>; color: white; padding: 6px 15px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                <?php echo htmlspecialchars($payment_status_labels[$order['payment_status_luxury']] ?? $order['payment_status_luxury']); ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <!-- Комментарий к заказу -->
                <?php if(!empty($order['notes_luxury'])): ?>
                <div style="margin-bottom: 30px;">
                    <h4 style="color: #555; margin-bottom: 15px; font-size: 16px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-sticky-note" style="color: #A0522D;"></i> Комментарий клиента
                    </h4>
                    <div style="background-color: #fcf9f5; padding: 20px; border-radius: 10px; border: 1px solid #f0e6d6;">
                        <p style="margin: 0; white-space: pre-line; line-height: 1.6; color: #555; display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fas fa-comment-dots" style="color: #A0522D; margin-top: 3px;"></i>
                            <span><?php echo htmlspecialchars($order['notes_luxury']); ?></span>
                        </p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Подарочная упаковка -->
                <?php if($order['gift_wrapping_luxury']): ?>
                <div style="margin-bottom: 30px;">
                    <div style="background: linear-gradient(135deg, #f8e8d8 0%, #f5e0cc 100%); padding: 20px; border-radius: 10px; border: 1px solid #e8d9c5; text-align: center;">
                        <div style="font-size: 32px; margin-bottom: 10px;">🎁</div>
                        <p style="margin: 0; color: #8B4513; font-weight: 600;">Подарочная упаковка</p>
                        <p style="margin: 5px 0 0 0; color: #A0522D; font-size: 14px;">Заказ будет красиво упакован</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Кнопки действий -->
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 20px;">
                    <a href="cabinet.php" 
                       style="padding: 14px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; text-decoration: none; border-radius: 8px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; border: 1px solid #e8d9c5; font-weight: 500;"
                       onmouseover="this.style.background='linear-gradient(135deg, #f5e6d3 0%, #f0e0c9 100%)'"
                       onmouseout="this.style.background='linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%)'">
                        <i class="fas fa-arrow-left"></i> Назад к заказам
                    </a>
                    
                    <?php if(isset($_SESSION['is_admin_luxury']) && $_SESSION['is_admin_luxury']): ?>
                        <a href="admin.php?tab=orders&edit=<?php echo $order['id']; ?>" 
                           style="padding: 14px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 8px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; font-weight: 500;"
                           onmouseover="this.style.background='linear-gradient(135deg, #A0522D 0%, #8B4513 100%)'"
                           onmouseout="this.style.background='linear-gradient(135deg, #8B4513 0%, #A0522D 100%)'">
                            <i class="fas fa-cog"></i> Управление заказом
                        </a>
                    <?php endif; ?>
                    
                    <?php if($order['payment_status_luxury'] != 'paid' && $order['status_luxury'] != 'cancelled'): ?>
                        <a href="checkout.php?order=<?php echo $order_id; ?>" 
                           style="padding: 14px; background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%); color: white; text-decoration: none; border-radius: 8px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s; font-weight: 500;"
                           onmouseover="this.style.background='linear-gradient(135deg, #FFD700 0%, #D4AF37 100%)'"
                           onmouseout="this.style.background='linear-gradient(135deg, #D4AF37 0%, #FFD700 100%)'">
                            <i class="fas fa-credit-card"></i> Оплатить заказ
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Маска для телефона
document.addEventListener('DOMContentLoaded', function() {
    const phoneElement = document.querySelector('span[style*="color: #333;"]');
    if (phoneElement && phoneElement.textContent.includes('+7')) {
        formatPhoneDisplay(phoneElement);
    }
    
    function formatPhoneDisplay(element) {
        const phone = element.textContent;
        const digits = phone.replace(/\D/g, '');
        
        if (digits.length === 11 || digits.length === 10) {
            const formatted = '+7 (' + digits.slice(-10, -7) + ') ' + 
                             digits.slice(-7, -4) + '-' + 
                             digits.slice(-4, -2) + '-' + 
                             digits.slice(-2);
            element.textContent = formatted;
        }
    }
});
</script>

<?php include 'footer.php'; ?>