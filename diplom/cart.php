<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Обработка действий с корзиной через AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $response = ['success' => false];
    
    if (isset($_POST['update_quantity'])) {
        $cart_item_id = intval($_POST['cart_item_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM luxury_cart WHERE id = ? AND user_id_luxury = ?");
            $stmt->execute([$cart_item_id, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE luxury_cart SET quantity_luxury = ? WHERE id = ? AND user_id_luxury = ?");
            $stmt->execute([$quantity, $cart_item_id, $_SESSION['user_id']]);
        }
        
        $response['success'] = true;
        $response['cart_item_id'] = $cart_item_id;
        $response['quantity'] = $quantity;
        
    } elseif (isset($_POST['remove_item'])) {
        $cart_item_id = intval($_POST['cart_item_id']);
        $stmt = $pdo->prepare("DELETE FROM luxury_cart WHERE id = ? AND user_id_luxury = ?");
        $stmt->execute([$cart_item_id, $_SESSION['user_id']]);
        $response['success'] = true;
        $response['cart_item_id'] = $cart_item_id;
        $response['removed'] = true;
        
    } elseif (isset($_POST['clear_cart'])) {
        $stmt = $pdo->prepare("DELETE FROM luxury_cart WHERE user_id_luxury = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $response['success'] = true;
        $response['cleared'] = true;
    }
    
    // Получаем обновленные данные корзины
    if ($response['success']) {
        $sql = "SELECT c.*, p.name_luxury, p.price_luxury, p.stock_luxury, 
                       p.image_luxury, p.material_luxury, cat.name_luxury as category_name
                FROM luxury_cart c
                JOIN luxury_jewelry p ON c.product_id_luxury = p.id
                LEFT JOIN luxury_categories cat ON p.category_id_luxury = cat.id
                WHERE c.user_id_luxury = ?
                ORDER BY c.added_at_luxury DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id']]);
        $cart_items = $stmt->fetchAll();
        
        $total_amount = 0;
        foreach ($cart_items as $item) {
            $total_amount += $item['price_luxury'] * $item['quantity_luxury'];
        }
        
        $response['total_amount'] = $total_amount;
        $response['item_count'] = count($cart_items);
        $response['shipping'] = ($total_amount > 10000) ? 0 : 1000;
        $response['grand_total'] = $total_amount + (($total_amount > 10000) ? 0 : 1000);
    }
    
    echo json_encode($response);
    exit();
}

// Получаем товары в корзине
try {
    $sql = "SELECT c.*, p.name_luxury, p.price_luxury, p.stock_luxury, 
                   p.image_luxury, p.material_luxury, cat.name_luxury as category_name
            FROM luxury_cart c
            JOIN luxury_jewelry p ON c.product_id_luxury = p.id
            LEFT JOIN luxury_categories cat ON p.category_id_luxury = cat.id
            WHERE c.user_id_luxury = ?
            ORDER BY c.added_at_luxury DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    // Подсчет общей суммы
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price_luxury'] * $item['quantity_luxury'];
    }
    
    $shipping_cost = ($total_amount > 10000) ? 0 : 1000;
    
} catch (PDOException $e) {
    die("Ошибка при получении корзины: " . $e->getMessage());
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #8B4513; margin-bottom: 30px; font-weight: 300;">Корзина</h1>
    
    <!-- Уведомление -->
    <div id="cart-notification" style="display: none; position: fixed; top: 100px; right: 20px; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 20px; border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; animation: slideIn 0.3s ease-out;">
        <span id="notification-message">Корзина обновлена</span>
        <span id="notification-close" style="margin-left: 15px; cursor: pointer; font-weight: bold;">×</span>
    </div>
    
    <?php if (empty($cart_items)): ?>
        <div style="text-align: center; padding: 60px; background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
            <div style="font-size: 5rem; margin-bottom: 20px; opacity: 0.3;">💎</div>
            <p style="color: #666; font-size: 18px; margin-bottom: 20px;">Ваша корзина пуста</p>
            <p style="color: #888; margin-bottom: 30px;">Добавьте понравившиеся ювелирные изделия в корзину</p>
            <a href="catalog.php" style="padding: 12px 30px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 5px; font-size: 16px;">
                Перейти в каталог
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Список изделий -->
            <div>
                <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h3 style="color: #333; margin: 0; font-weight: 400;" id="cart-title">
                            Изделия в корзине (<span id="item-count"><?php echo count($cart_items); ?></span> шт.)
                        </h3>
                        <button type="button" onclick="clearCart()" 
                                style="padding: 8px 15px; background: #f8f9fa; color: #dc3545; border: 1px solid #dc3545; border-radius: 5px; cursor: pointer; font-size: 14px; transition: all 0.3s;">
                            Очистить корзину
                        </button>
                    </div>
                    
                    <div id="cart-items-container">
                        <?php foreach ($cart_items as $item): ?>
                            <div id="cart-item-<?php echo $item['id']; ?>" style="display: flex; gap: 20px; padding: 25px 0; border-bottom: 1px solid #eee;">
                                <div style="width: 120px; height: 120px; background-color: #f9f9f9; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border: 1px solid #e8e8e8; overflow: hidden;">
                                    <?php if (!empty($item['image_luxury'])): ?>
                                        <img src="<?php echo htmlspecialchars($item['image_luxury']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['name_luxury']); ?>"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 2rem;">💎</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                        <div>
                                            <h4 style="margin: 0 0 8px 0; color: #333; font-weight: 400;"><?php echo htmlspecialchars($item['name_luxury']); ?></h4>
                                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                                <span style="background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                                    <?php echo htmlspecialchars($item['category_name']); ?>
                                                </span>
                                                <?php if($item['material_luxury']): ?>
                                                    <span style="background-color: #e6f7ff; color: #0066cc; padding: 3px 10px; border-radius: 12px; font-size: 12px;">
                                                        <?php echo htmlspecialchars($item['material_luxury']); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="font-size: 20px; font-weight: 500; color: #D4AF37;">
                                            <?php echo number_format($item['price_luxury'], 0, ',', ' '); ?> ₽
                                        </div>
                                    </div>
                                    
                                    <div style="color: <?php echo ($item['stock_luxury'] > 0) ? '#28a745' : '#dc3545'; ?>; font-size: 14px; margin-bottom: 15px; display: flex; align-items: center; gap: 5px;">
                                        <?php if($item['stock_luxury'] > 0): ?>
                                            <span>✓</span> В наличии: <?php echo $item['stock_luxury']; ?> шт.
                                        <?php else: ?>
                                            <span>✗</span> Нет в наличии
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <button type="button" onclick="changeQuantity(<?php echo $item['id']; ?>, -1, <?php echo $item['stock_luxury']; ?>)" 
                                                        style="width: 35px; height: 35px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; font-size: 16px; transition: all 0.3s;">
                                                    -
                                                </button>
                                                
                                                <input type="number" 
                                                       id="quantity-<?php echo $item['id']; ?>" 
                                                       value="<?php echo $item['quantity_luxury']; ?>" 
                                                       min="1" 
                                                       max="<?php echo $item['stock_luxury']; ?>"
                                                       style="width: 70px; text-align: center; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                                                
                                                <button type="button" onclick="changeQuantity(<?php echo $item['id']; ?>, 1, <?php echo $item['stock_luxury']; ?>)" 
                                                        style="width: 35px; height: 35px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; font-size: 16px; transition: all 0.3s;">
                                                    +
                                                </button>
                                            </div>
                                            <button type="button" onclick="updateQuantity(<?php echo $item['id']; ?>)" 
                                                    style="padding: 6px 12px; background-color: #f8f9fa; color: #666; border: 1px solid #ddd; border-radius: 5px; cursor: pointer; font-size: 12px; transition: all 0.3s;">
                                                Обновить
                                            </button>
                                        </div>
                                        
                                        <button type="button" onclick="removeItem(<?php echo $item['id']; ?>)" 
                                                style="padding: 8px 16px; background-color: #f8f9fa; color: #dc3545; border: 1px solid #dc3545; border-radius: 5px; cursor: pointer; font-size: 14px; transition: all 0.3s;">
                                            Удалить
                                        </button>
                                    </div>
                                    
                                    <div style="text-align: right; margin-top: 15px;">
                                        <span id="item-total-<?php echo $item['id']; ?>" style="font-size: 18px; font-weight: 500; color: #333;">
                                            Итого: <span style="color: #D4AF37;"><?php echo number_format($item['price_luxury'] * $item['quantity_luxury'], 0, ',', ' '); ?> ₽</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Гарантии -->
                    <div style="margin-top: 30px; padding: 20px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 10px; border: 1px solid #e8e8e8;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span style="font-size: 24px;">🛡️</span>
                            <div>
                                <h4 style="margin: 0 0 5px 0; color: #8B4513;">Гарантии Luxury</h4>
                                <p style="margin: 0; color: #666; font-size: 14px;">
                                    • Гарантия на все изделия 2 года<br>
                                    • Бесплатная доставка при заказе от 10,000 ₽<br>
                                    • Возврат в течение 14 дней<br>
                                    • Сертификаты подлинности
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Итоговая информация -->
            <div>
                <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8; position: sticky; top: 20px;">
                    <h3 style="color: #333; margin-bottom: 20px; font-weight: 400;">Ваш заказ</h3>
                    
                    <div style="margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #666;">Изделия (<span id="summary-item-count"><?php echo count($cart_items); ?></span> шт.)</span>
                            <span id="summary-total" style="font-weight: 500;"><?php echo number_format($total_amount, 0, ',', ' '); ?> ₽</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: #666;">Доставка</span>
                            <span id="summary-shipping" style="font-weight: 500;">
                                <?php if($total_amount > 10000): ?>
                                    <span style="color: #28a745;">Бесплатно</span>
                                <?php else: ?>
                                    1,000 ₽
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <?php if($total_amount < 10000): ?>
                            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px; margin-bottom: 15px;">
                                <div style="color: #856404; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                                    <span>💡</span>
                                    <span>Добавьте ещё <?php echo number_format(10000 - $total_amount, 0, ',', ' '); ?> ₽ для бесплатной доставки</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div style="border-top: 2px solid #eee; margin: 20px 0; padding-top: 20px;">
                            <div style="display: flex; justify-content: space-between; font-size: 22px; font-weight: 500;">
                                <span>Итого к оплате:</span>
                                <span id="summary-grand-total" style="color: #D4AF37;"><?php echo number_format($total_amount + $shipping_cost, 0, ',', ' '); ?> ₽</span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="checkout.php" 
                       style="display: block; width: 100%; padding: 15px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-align: center; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: 500; margin-bottom: 15px; transition: all 0.3s;">
                        Перейти к оформлению
                    </a>
                    
                    <a href="catalog.php" 
                       style="display: block; width: 100%; padding: 12px; background-color: #f8f9fa; color: #666; text-align: center; text-decoration: none; border-radius: 8px; border: 1px solid #ddd; transition: all 0.3s;">
                        Продолжить покупки
                    </a>
                    
                    <!-- Промокод -->
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                        <h4 style="color: #333; margin-bottom: 10px; font-size: 14px; font-weight: 500;">Промокод</h4>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" placeholder="Введите промокод" 
                                   style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <button style="padding: 10px 20px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
                                Применить
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Подарочная упаковка -->
                <div style="background: white; border-radius: 15px; padding: 20px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); margin-top: 20px; border: 1px solid #e8e8e8;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <span style="font-size: 24px;">🎁</span>
                        <div>
                            <h4 style="margin: 0 0 5px 0; color: #333; font-weight: 400;">Подарочная упаковка</h4>
                            <p style="margin: 0; color: #666; font-size: 14px;">Добавьте элегантную подарочную упаковку за 500 ₽</p>
                        </div>
                    </div>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" style="width: 18px; height: 18px;">
                        <span style="color: #666;">Добавить подарочную упаковку (+500 ₽)</span>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Дополнительная кнопка оформления -->
        <div style="margin-top: 30px; background: white; border-radius: 15px; padding: 25px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8; display: none;">
            <div style="text-align: center;">
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 24px; font-weight: 500; color: #D4AF37; margin-bottom: 10px;">
                        Итого к оплате: <span id="bottom-grand-total"><?php echo number_format($total_amount + $shipping_cost, 0, ',', ' '); ?></span> ₽
                    </div>
                    <div style="color: #666; font-size: 14px;">
                        <?php if($total_amount > 10000): ?>
                            Бесплатная доставка включена
                        <?php else: ?>
                            Включая доставку 1,000 ₽ • Бесплатная доставка от 10,000 ₽
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="checkout.php" 
                       style="padding: 15px 40px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 8px; font-size: 18px; font-weight: 500; min-width: 200px; text-align: center;">
                        Оформить заказ
                    </a>
                    
                    <a href="catalog.php" 
                       style="padding: 15px 30px; background-color: #f8f9fa; color: #666; text-decoration: none; border-radius: 8px; border: 1px solid #ddd; font-size: 16px; min-width: 200px; text-align: center;">
                        Продолжить покупки
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translateX(0);
    }
    to {
        opacity: 0;
        transform: translateX(-20px);
    }
}

.item-removed {
    animation: fadeOut 0.3s ease-out;
    opacity: 0;
}

/* Адаптация для мобильных */
@media (max-width: 768px) {
    div[style*="display: grid; grid-template-columns: 2fr 1fr"] {
        display: flex !important;
        flex-direction: column !important;
        gap: 20px !important;
    }
    
    div[style*="display: flex; gap: 20px; padding: 25px 0"] {
        flex-direction: column;
        gap: 15px !important;
    }
    
    div[style*="width: 120px; height: 120px"] {
        width: 100% !important;
        height: 200px !important;
    }
    
    div[style*="flex: 1"] {
        width: 100% !important;
    }
    
    div[style*="display: flex; justify-content: space-between; align-items: flex-start"] {
        flex-direction: column;
        gap: 10px !important;
    }
    
    div[style*="display: flex; justify-content: space-between; align-items: center"] {
        flex-direction: column;
        align-items: stretch !important;
        gap: 15px !important;
    }
    
    div[style*="position: sticky; top: 20px"] {
        position: static !important;
    }
    
    div[style*="display: none"] {
        display: block !important;
    }
    
    div[style*="display: flex; gap: 15px; justify-content: center"] {
        flex-direction: column;
        align-items: center !important;
    }
    
    a[style*="min-width: 200px"] {
        width: 100% !important;
        min-width: unset !important;
    }
}

@media (min-width: 769px) {
    div[style*="display: none"] {
        display: none !important;
    }
}
</style>

<script>
let notificationTimeout;

function showNotification(message, type = 'success') {
    const notification = document.getElementById('cart-notification');
    const messageElement = document.getElementById('notification-message');
    
    const colors = {
        'success': 'linear-gradient(135deg, #28a745, #20c997)',
        'error': 'linear-gradient(135deg, #dc3545, #e83e8c)'
    };
    
    notification.style.background = colors[type] || colors.success;
    messageElement.textContent = message;
    notification.style.display = 'block';
    notification.style.animation = 'slideIn 0.3s ease-out';
    
    clearTimeout(notificationTimeout);
    notificationTimeout = setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            notification.style.display = 'none';
        }, 300);
    }, 3000);
}

function changeQuantity(cartItemId, change, maxStock) {
    const input = document.getElementById(`quantity-${cartItemId}`);
    if (!input) return;
    
    let quantity = parseInt(input.value) + change;
    
    if (quantity < 1) quantity = 1;
    if (quantity > maxStock) quantity = maxStock;
    
    input.value = quantity;
}

function updateQuantity(cartItemId) {
    const input = document.getElementById(`quantity-${cartItemId}`);
    if (!input) return;
    
    const quantity = parseInt(input.value);
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `ajax=1&update_quantity=1&cart_item_id=${cartItemId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Количество обновлено');
            
            const itemElement = document.getElementById(`cart-item-${cartItemId}`);
            const priceElement = itemElement.querySelector('div[style*="font-size: 20px"]');
            const priceText = priceElement.textContent.trim();
            const price = parseInt(priceText.replace(/[^\d]/g, ''));
            
            const itemTotalElement = document.getElementById(`item-total-${cartItemId}`);
            const itemTotal = price * quantity;
            itemTotalElement.innerHTML = `Итого: <span style="color: #D4AF37;">${itemTotal.toLocaleString('ru-RU')} ₽</span>`;
            
            updateSummary(data);
            updateHeaderCartCount(data.item_count);
            
            const cartTitleElement = document.getElementById('cart-title');
            if (cartTitleElement) {
                cartTitleElement.innerHTML = `Изделия в корзине (<span id="item-count">${data.item_count}</span> шт.)`;
            }
            
            if (quantity <= 0) {
                setTimeout(() => {
                    itemElement.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        itemElement.remove();
                        checkEmptyCart();
                    }, 300);
                }, 500);
            }
        } else {
            showNotification('Ошибка при обновлении', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при обновлении', 'error');
    });
}

function removeItem(cartItemId) {
    if (!confirm('Удалить изделие из корзины?')) return;
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `ajax=1&remove_item=1&cart_item_id=${cartItemId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Изделие удалено из корзины');
            
            const itemElement = document.getElementById(`cart-item-${cartItemId}`);
            itemElement.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                itemElement.remove();
                updateSummary(data);
                updateHeaderCartCount(data.item_count);
                checkEmptyCart();
            }, 300);
        } else {
            showNotification('Ошибка при удалении', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при удалении', 'error');
    });
}

function clearCart() {
    if (!confirm('Очистить всю корзину?')) return;
    
    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ajax=1&clear_cart=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Корзина очищена');
            
            const cartItems = document.querySelectorAll('[id^="cart-item-"]');
            cartItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => item.remove(), 300);
                }, index * 100);
            });
            
            setTimeout(() => {
                updateSummary(data);
                updateHeaderCartCount(0);
                checkEmptyCart();
            }, cartItems.length * 100 + 300);
        } else {
            showNotification('Ошибка при очистке корзины', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при очистке корзины', 'error');
    });
}

function updateSummary(data) {
    const itemCountElement = document.getElementById('item-count');
    const summaryItemCountElement = document.getElementById('summary-item-count');
    
    if (itemCountElement) itemCountElement.textContent = data.item_count;
    if (summaryItemCountElement) summaryItemCountElement.textContent = data.item_count;
    
    const summaryTotalElement = document.getElementById('summary-total');
    const summaryShippingElement = document.getElementById('summary-shipping');
    const summaryGrandTotalElement = document.getElementById('summary-grand-total');
    const bottomGrandTotalElement = document.getElementById('bottom-grand-total');
    
    if (summaryTotalElement) summaryTotalElement.textContent = data.total_amount.toLocaleString('ru-RU') + ' ₽';
    
    if (data.total_amount > 10000) {
        if (summaryShippingElement) summaryShippingElement.innerHTML = '<span style="color: #28a745;">Бесплатно</span>';
    } else {
        if (summaryShippingElement) summaryShippingElement.textContent = '1,000 ₽';
    }
    
    if (summaryGrandTotalElement) summaryGrandTotalElement.textContent = data.grand_total.toLocaleString('ru-RU') + ' ₽';
    if (bottomGrandTotalElement) bottomGrandTotalElement.textContent = data.grand_total.toLocaleString('ru-RU');
    
    const cartTitleElement = document.querySelector('#cart-title span[id="item-count"]');
    if (cartTitleElement) {
        cartTitleElement.textContent = data.item_count;
    }
}

function updateHeaderCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.style.display = count > 0 ? 'inline' : 'none';
    }
}

function checkEmptyCart() {
    const cartItems = document.querySelectorAll('[id^="cart-item-"]');
    if (cartItems.length === 0) {
        setTimeout(() => {
            location.reload();
        }, 500);
    }
}

// Закрытие уведомления
document.getElementById('notification-close')?.addEventListener('click', function() {
    const notification = document.getElementById('cart-notification');
    notification.style.animation = 'slideOut 0.3s ease-out';
    setTimeout(() => {
        notification.style.display = 'none';
    }, 300);
    clearTimeout(notificationTimeout);
});

// Автоматическое обновление при изменении количества
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('input[id^="quantity-"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartItemId = this.id.replace('quantity-', '');
            updateQuantity(cartItemId);
        });
    });
});
</script>

<?php include 'footer.php'; ?>