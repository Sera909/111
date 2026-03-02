<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем информацию о пользователе
try {
    $stmt = $pdo->prepare("SELECT * FROM luxury_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Получаем заказы пользователя
    $stmt = $pdo->prepare("SELECT * FROM luxury_orders WHERE user_id_luxury = ? ORDER BY created_at_luxury DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
    
    // Подсчитываем статистику
    $total_orders = count($orders);
    $active_orders = 0;
    $total_spent = 0;
    $vip_status = $user['is_vip_luxury'] ?? false;
    
    foreach ($orders as $order) {
        $total_spent += $order['total_amount_luxury'];
        if ($order['status_luxury'] != 'cancelled' && $order['status_luxury'] != 'delivered') {
            $active_orders++;
        }
    }
    
    // Проверяем, является ли пользователь VIP (если потратил более 100,000 ₽)
    if (!$vip_status && $total_spent > 100000) {
        $vip_status = true;
        // Обновляем статус в базе
        $stmt = $pdo->prepare("UPDATE luxury_users SET is_vip_luxury = 1 WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }
    
} catch (PDOException $e) {
    die("Ошибка при получении данных: " . $e->getMessage());
}

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $birthday = trim($_POST['birthday']);
    
    try {
        $stmt = $pdo->prepare("UPDATE luxury_users SET phone_luxury = ?, address_luxury = ?, birthday_luxury = ? WHERE id = ?");
        $stmt->execute([$phone, $address, $birthday, $_SESSION['user_id']]);
        $success_message = "Профиль успешно обновлен!";
        
        // Обновляем данные в сессии
        $_SESSION['phone_luxury'] = $phone;
        $_SESSION['address_luxury'] = $address;
        
        // Перезагружаем данные пользователя
        $stmt = $pdo->prepare("SELECT * FROM luxury_users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
    } catch (PDOException $e) {
        $error_message = "Ошибка при обновлении профиля: " . $e->getMessage();
    }
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <h1 style="color: <?php echo getSetting('primary_color', '#8B4513'); ?>; margin-bottom: 30px; font-weight: 300;">Личный кабинет</h1>
    
    <!-- VIP Badge -->
    <?php if($vip_status): ?>
        <div style="background: linear-gradient(135deg, #D4AF37, #FFD700); color: white; padding: 15px 25px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 15px;">
            <span style="font-size: 24px;">👑</span>
            <div>
                <h3 style="margin: 0 0 5px 0;">VIP КЛИЕНТ</h3>
                <p style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Вы получаете эксклюзивные предложения и приоритетное обслуживание</p>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Уведомления -->
    <?php if(isset($success_message)): ?>
        <div style="background-color: <?php echo getSetting('success_color', '#d4edda'); ?>; color: <?php echo getSetting('primary_color', '#155724'); ?>; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            ✅ <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div style="background-color: <?php echo getSetting('error_color', '#f8d7da'); ?>; color: <?php echo getSetting('error_color', '#721c24'); ?>; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            ❌ <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Статистика -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border: 1px solid #e8e8e8;">
            <h3 style="color: #333; margin-bottom: 10px; font-weight: 400;">Всего заказов</h3>
            <div style="font-size: 32px; font-weight: bold; color: <?php echo getSetting('primary_color', '#8B4513'); ?>;"><?php echo $total_orders; ?></div>
            <div style="font-size: 12px; color: #666;">Заказов в обработке: <?php echo $active_orders; ?></div>
        </div>
        
        <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border: 1px solid #e8e8e8;">
            <h3 style="color: #333; margin-bottom: 10px; font-weight: 400;">Общая сумма покупок</h3>
            <div style="font-size: 32px; font-weight: bold; color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;"><?php echo number_format($total_spent, 0, ',', ' '); ?> ₽</div>
            <?php if($total_spent >= 100000): ?>
                <div style="font-size: 12px; color: #D4AF37;">🏆 Достигнут VIP статус!</div>
            <?php else: ?>
                <div style="font-size: 12px; color: #666;">До VIP статуса осталось: <?php echo number_format(100000 - $total_spent, 0, ',', ' '); ?> ₽</div>
            <?php endif; ?>
        </div>
        
        <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border: 1px solid #e8e8e8;">
            <h3 style="color: #333; margin-bottom: 10px; font-weight: 400;">Скидка</h3>
            <div style="font-size: 32px; font-weight: bold; color: <?php echo getSetting('secondary_color', '#A0522D'); ?>;">
                <?php 
                    if ($total_orders >= 10) echo "10%";
                    elseif ($total_orders >= 5) echo "5%";
                    else echo "0%";
                ?>
            </div>
            <div style="font-size: 12px; color: #666;">
                <?php 
                    if ($total_orders >= 10) echo "Постоянная скидка 10%";
                    elseif ($total_orders >= 5) echo "5% скидка на следующий заказ";
                    else echo "После 5 заказов - скидка 5%";
                ?>
            </div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
        <!-- Профиль -->
        <div>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border: 1px solid #e8e8e8;">
                <h3 style="color: #333; margin-bottom: 20px; font-weight: 400;">Мой профиль</h3>
                
                <div style="margin-bottom: 20px;">
                    <p><strong>Имя:</strong> <?php echo htmlspecialchars($user['username_luxury']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email_luxury']); ?></p>
                    <p><strong>Телефон:</strong> <?php echo htmlspecialchars($user['phone_luxury'] ?: 'Не указан'); ?></p>
                    <p><strong>Адрес:</strong> <?php echo nl2br(htmlspecialchars($user['address_luxury'] ?: 'Не указан')); ?></p>
                    <p><strong>Дата рождения:</strong> <?php echo $user['birthday_luxury'] ? date('d.m.Y', strtotime($user['birthday_luxury'])) : 'Не указана'; ?></p>
                    <p><strong>Дата регистрации:</strong> <?php echo date('d.m.Y', strtotime($user['registration_date_luxury'])); ?></p>
                    <p><strong>Статус:</strong> 
                        <?php if($vip_status): ?>
                            <span style="background: linear-gradient(135deg, #D4AF37, #FFD700); color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px;">VIP КЛИЕНТ</span>
                        <?php else: ?>
                            <span style="background-color: <?php echo getSetting('secondary_color', '#A0522D'); ?>; color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px;">СТАНДАРТ</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <button onclick="showEditForm()" 
                        style="width: 100%; padding: 12px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: all 0.3s;">
                    Редактировать профиль
                </button>
                
                <!-- Форма редактирования профиля -->
                <div id="edit-profile-form" style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                    <form method="POST" action="">
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px;">Телефон</label>
                            <input type="tel" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone_luxury']); ?>"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                                   placeholder="+7 (999) 999-99-99">
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px;">Адрес доставки</label>
                            <textarea name="address" rows="3"
                                      style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><?php echo htmlspecialchars($user['address_luxury']); ?></textarea>
                        </div>
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px;">Дата рождения</label>
                            <input type="date" name="birthday" 
                                   value="<?php echo $user['birthday_luxury']; ?>"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="submit" name="update_profile" 
                                    style="flex: 1; padding: 10px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 5px; cursor: pointer;">
                                Сохранить
                            </button>
                            <button type="button" onclick="hideEditForm()"
                                    style="flex: 1; padding: 10px; background-color: #f5f5f5; color: #666; border: 1px solid #ddd; border-radius: 5px; cursor: pointer;">
                                Отмена
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Избранное -->
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-top: 20px; border: 1px solid #e8e8e8;">
                <h3 style="color: #333; margin-bottom: 15px; font-weight: 400;">❤️ Избранное</h3>
                <p style="color: #666; margin-bottom: 15px;">Сохраняйте понравившиеся изделия для быстрого доступа</p>
                <a href="wishlist.php" style="display: block; padding: 10px; text-align: center; background: #f9f9f9; color: #8B4513; text-decoration: none; border-radius: 5px; border: 1px solid #e8e8e8;">
                    Перейти в избранное →
                </a>
            </div>
        </div>
        
        <!-- История заказов -->
        <div>
            <div style="background: <?php echo getSetting('card_color', 'white'); ?>; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); border: 1px solid #e8e8e8;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="color: #333; margin: 0; font-weight: 400;">История заказов</h3>
                    <span style="color: #666; font-size: 14px;">
                        Всего: <?php echo $total_orders; ?> заказов
                    </span>
                </div>
                
                <?php if (empty($orders)): ?>
                    <div style="text-align: center; padding: 40px;">
                        <p style="color: #666; font-size: 16px; margin-bottom: 20px;">У вас пока нет заказов</p>
                        <a href="catalog.php" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 5px;">
                            Перейти в каталог
                        </a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, <?php echo getSetting('primary_color', '#8B4513'); ?>, <?php echo getSetting('secondary_color', '#A0522D'); ?>); color: white;">
                                    <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">№ Заказа</th>
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
                                        <strong style="color: <?php echo getSetting('primary_color', '#8B4513'); ?>;"><?php echo htmlspecialchars($order['order_number_luxury']); ?></strong>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php echo date('d.m.Y', strtotime($order['created_at_luxury'])); ?>
                                    </td>
                                    <td style="padding: 12px; font-weight: bold; color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;">
                                        <?php echo number_format($order['total_amount_luxury'], 0, ',', ' '); ?> ₽
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php
                                        $status_colors = [
                                            'pending' => '#ffc107',
                                            'processing' => '#17a2b8',
                                            'shipped' => '#6f42c1',
                                            'delivered' => '#28a745',
                                            'cancelled' => '#dc3545'
                                        ];
                                        
                                        $status_labels = [
                                            'pending' => 'Ожидает обработки',
                                            'processing' => 'В обработке',
                                            'shipped' => 'Отправлен',
                                            'delivered' => 'Доставлен',
                                            'cancelled' => 'Отменен'
                                        ];
                                        
                                        $status = $order['status_luxury'];
                                        $color = $status_colors[$status] ?? '#666';
                                        $label = $status_labels[$status] ?? $status;
                                        ?>
                                        <span style="background-color: <?php echo $color; ?>; color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                            <?php echo htmlspecialchars($label); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 12px;">
                                        <a href="order.php?id=<?php echo $order['id']; ?>" 
                                           style="padding: 5px 10px; background-color: #f5f5f5; color: #666; text-decoration: none; border-radius: 3px; font-size: 12px; border: 1px solid #ddd; transition: all 0.3s;">
                                            Подробнее
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Кнопка "Повторить заказ" для последнего заказа -->
                    <?php if (!empty($orders) && $orders[0]['status_luxury'] == 'delivered'): ?>
                        <div style="margin-top: 20px; padding: 15px; background: #f9f9f9; border-radius: 5px; border: 1px solid #e8e8e8;">
                            <h4 style="color: #333; margin: 0 0 10px 0; font-weight: 400;">Понравился последний заказ?</h4>
                            <a href="reorder.php?order_id=<?php echo $orders[0]['id']; ?>" 
                               style="display: inline-block; padding: 8px 16px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 3px; font-size: 14px;">
                                Повторить заказ
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Специальные предложения -->
            <?php if($vip_status): ?>
                <div style="background: linear-gradient(135deg, #D4AF37, #FFD700); color: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 15px 0; font-weight: 400;">🎁 Специальное предложение для VIP</h3>
                    <p style="margin: 0 0 15px 0; opacity: 0.9;">При покупке от 50,000 ₽ получите в подарок серебряные серьги!</p>
                    <a href="catalog.php" style="display: inline-block; padding: 8px 16px; background: white; color: #D4AF37; text-decoration: none; border-radius: 3px; font-weight: 600;">
                        Использовать предложение →
                    </a>
                </div>
            <?php elseif($total_orders >= 3): ?>
                <div style="background: linear-gradient(135deg, <?php echo getSetting('primary_color', '#8B4513'); ?>, <?php echo getSetting('secondary_color', '#A0522D'); ?>); color: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 15px 0; font-weight: 400;">🎁 Персональная скидка</h3>
                    <p style="margin: 0 0 15px 0; opacity: 0.9;">За <?php echo $total_orders; ?> заказов вы получаете скидку 5% на следующий заказ!</p>
                    <p style="margin: 0; font-size: 12px; opacity: 0.8;">Промокод: VIP<?php echo str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showEditForm() {
    document.getElementById('edit-profile-form').style.display = 'block';
}

function hideEditForm() {
    document.getElementById('edit-profile-form').style.display = 'none';
}

// Маска для телефона в форме редактирования
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.startsWith('7') || value.startsWith('8')) {
                value = value.substring(1);
            }
            
            let formattedValue = '+7';
            if (value.length > 0) {
                formattedValue += ' (' + value.substring(0, 3);
            }
            if (value.length > 3) {
                formattedValue += ') ' + value.substring(3, 6);
            }
            if (value.length > 6) {
                formattedValue += '-' + value.substring(6, 8);
            }
            if (value.length > 8) {
                formattedValue += '-' + value.substring(8, 10);
            }
            
            this.value = formattedValue;
        });
    }
});
</script>

<?php include 'footer.php'; ?>