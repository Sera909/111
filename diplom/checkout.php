<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем изделия в корзине
try {
    $sql = "SELECT c.*, p.name_luxury, p.price_luxury, p.stock_luxury, p.material_luxury
            FROM luxury_cart c
            JOIN luxury_jewelry p ON c.product_id_luxury = p.id
            WHERE c.user_id_luxury = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        header('Location: cart.php');
        exit();
    }
    
    // Проверяем наличие всех товаров
    foreach ($cart_items as $item) {
        if ($item['stock_luxury'] < $item['quantity_luxury']) {
            $error_message = "Товар '{$item['name_luxury']}' недоступен в запрошенном количестве. Доступно: {$item['stock_luxury']} шт.";
            break;
        }
    }
    
    // Подсчет общей суммы
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price_luxury'] * $item['quantity_luxury'];
    }
    
    // Получаем информацию о пользователе
    $stmt = $pdo->prepare("SELECT * FROM luxury_users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Проверяем VIP статус для скидки
    $is_vip = $user['is_vip_luxury'] ?? false;
    $discount_percent = 0;
    
    if ($is_vip) {
        $discount_percent = 10; // VIP клиенты получают 10% скидку
    } elseif (isset($_SESSION['order_count']) && $_SESSION['order_count'] >= 5) {
        $discount_percent = 5; // Постоянные клиенты после 5 заказов
    }
    
    $discount_amount = ($total_amount * $discount_percent) / 100;
    $subtotal = $total_amount - $discount_amount;
    $shipping_cost = ($subtotal > 10000) ? 0 : 1000;
    $gift_wrapping = 500; // Стоимость подарочной упаковки
    
} catch (PDOException $e) {
    die("Ошибка при получении данных: " . $e->getMessage());
}

// Обработка оформления заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, что корзина не изменилась во время оформления
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM luxury_cart WHERE user_id_luxury = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetch()['count'];
    
    if ($cart_count != count($cart_items)) {
        $error_message = "Содержимое корзины изменилось. Пожалуйста, обновите страницу.";
    } else {
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $address = trim($_POST['address']);
        $payment_method = trim($_POST['payment_method']);
        $delivery_method = trim($_POST['delivery_method']);
        $gift_wrapping_requested = isset($_POST['gift_wrapping']) ? 1 : 0;
        $comment = trim($_POST['comment']);
        $promo_code = trim($_POST['promo_code']);
        
        // Валидация данных
        if (empty($name) || empty($phone) || empty($email) || empty($address)) {
            $error_message = "Пожалуйста, заполните все обязательные поля";
        } else {
            // Проверка промокода
            $promo_discount = 0;
            if (!empty($promo_code)) {
                // В реальном проекте здесь будет проверка промокода в базе данных
                if (strtoupper($promo_code) === 'LUXURY10') {
                    $promo_discount = ($subtotal * 10) / 100;
                }
            }
            
            $final_total = $subtotal + $shipping_cost - $promo_discount;
            if ($gift_wrapping_requested) {
                $final_total += $gift_wrapping;
            }
            
            // Получаем данные банковской карты, если выбран соответствующий способ оплаты
            $card_data = [];
            if ($payment_method === 'card') {
                $card_data['card_number'] = isset($_POST['card_number']) ? str_replace(' ', '', trim($_POST['card_number'])) : '';
                $card_data['card_expiry'] = trim($_POST['card_expiry'] ?? '');
                $card_data['card_cvc'] = trim($_POST['card_cvc'] ?? '');
                $card_data['card_holder'] = trim($_POST['card_holder'] ?? '');
            }
            
            try {
                // Генерируем номер заказа
                $order_number = 'LUX-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
                
                // Начинаем транзакцию
                $pdo->beginTransaction();
                
                // Создаем заказ
                $stmt = $pdo->prepare("INSERT INTO luxury_orders (
                    user_id_luxury, 
                    order_number_luxury, 
                    total_amount_luxury,
                    subtotal_luxury,
                    discount_amount_luxury,
                    shipping_cost_luxury,
                    promo_discount_luxury,
                    gift_wrapping_luxury,
                    shipping_address_luxury,
                    delivery_method_luxury,
                    customer_name_luxury,
                    customer_phone_luxury,
                    customer_email_luxury,
                    payment_method_luxury,
                    notes_luxury,
                    card_data_luxury
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt->execute([
                    $_SESSION['user_id'],
                    $order_number,
                    $final_total,
                    $subtotal,
                    $discount_amount,
                    $shipping_cost,
                    $promo_discount,
                    $gift_wrapping_requested,
                    $address,
                    $delivery_method,
                    $name,
                    $phone,
                    $email,
                    $payment_method,
                    $comment,
                    !empty($card_data) ? json_encode($card_data, JSON_UNESCAPED_UNICODE) : NULL
                ]);
                
                $order_id = $pdo->lastInsertId();
                
                // Добавляем изделия в заказ
                foreach ($cart_items as $item) {
                    $subtotal_item = $item['price_luxury'] * $item['quantity_luxury'];
                    
                    $stmt = $pdo->prepare("INSERT INTO luxury_order_items (
                        order_id_luxury,
                        product_id_luxury,
                        product_name_luxury,
                        product_price_luxury,
                        product_material_luxury,
                        quantity_luxury,
                        subtotal_luxury
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    
                    $stmt->execute([
                        $order_id,
                        $item['product_id_luxury'],
                        $item['name_luxury'],
                        $item['price_luxury'],
                        $item['material_luxury'],
                        $item['quantity_luxury'],
                        $subtotal_item
                    ]);
                    
                    // Обновляем количество изделия на складе
                    $stmt = $pdo->prepare("UPDATE luxury_jewelry 
                                          SET stock_luxury = stock_luxury - ? 
                                          WHERE id = ? AND stock_luxury >= ?");
                    $stmt->execute([$item['quantity_luxury'], $item['product_id_luxury'], $item['quantity_luxury']]);
                }
                
                // Добавляем запись в историю заказа
                $stmt = $pdo->prepare("INSERT INTO luxury_order_history (
                    order_id_luxury,
                    status_luxury,
                    comment_luxury,
                    changed_by_luxury
                ) VALUES (?, 'pending', 'Заказ создан', ?)");
                
                $stmt->execute([$order_id, $_SESSION['user_id']]);
                
                // Обновляем статистику пользователя
                $stmt = $pdo->prepare("UPDATE luxury_users 
                                      SET order_count_luxury = order_count_luxury + 1,
                                          total_spent_luxury = total_spent_luxury + ?
                                      WHERE id = ?");
                $stmt->execute([$final_total, $_SESSION['user_id']]);
                
                // Проверяем, стал ли пользователь VIP
                $new_total_spent = $user['total_spent_luxury'] + $final_total;
                if ($new_total_spent > 100000 && !$is_vip) {
                    $stmt = $pdo->prepare("UPDATE luxury_users SET is_vip_luxury = 1 WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                }
                
                // Очищаем корзину
                $stmt = $pdo->prepare("DELETE FROM luxury_cart WHERE user_id_luxury = ?");
                $stmt->execute([$_SESSION['user_id']]);
                
                $pdo->commit();
                
                // Отправляем email подтверждение (в реальном проекте)
                $success_message = "Заказ успешно оформлен!";
                $order_completed = true;
                $order_details = [
                    'number' => $order_number,
                    'total' => $final_total,
                    'email' => $email
                ];
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = "Ошибка при оформлении заказа: " . $e->getMessage();
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<style>
/* Luxury Checkout Styles */
.checkout-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.checkout-title {
    color: #8B4513;
    margin-bottom: 30px;
    font-weight: 300;
    letter-spacing: 1px;
}

/* Успешное оформление */
.order-success {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.05);
    border: 1px solid #e8e8e8;
    text-align: center;
}

.success-icon {
    font-size: 80px;
    margin-bottom: 20px;
    color: #28a745;
}

.order-number {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    padding: 10px 30px;
    border-radius: 25px;
    font-size: 24px;
    font-weight: 500;
    margin: 20px auto;
    display: inline-block;
}

/* Основной контейнер оформления */
.checkout-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.checkout-card {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.05);
    border: 1px solid #e8e8e8;
    margin-bottom: 20px;
}

.checkout-card-title {
    color: #8B4513;
    margin-bottom: 25px;
    font-weight: 400;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

/* Формы */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #555;
    font-weight: 500;
    font-size: 14px;
}

.form-label.required:after {
    content: " *";
    color: #dc3545;
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
    background: #f9f9f9;
}

.form-input:focus {
    outline: none;
    border-color: #8B4513;
    background: white;
    box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
}

.form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    resize: vertical;
    min-height: 100px;
    background: #f9f9f9;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

/* Радио кнопки и чекбоксы */
.option-group {
    margin-bottom: 15px;
}

.option-item {
    display: flex;
    align-items: center;
    padding: 15px;
    margin-bottom: 10px;
    background: #f9f9f9;
    border-radius: 10px;
    border: 2px solid #eee;
    cursor: pointer;
    transition: all 0.3s;
}

.option-item:hover {
    border-color: #8B4513;
    background: rgba(139, 69, 19, 0.05);
}

.option-item.selected {
    border-color: #8B4513;
    background: rgba(139, 69, 19, 0.1);
}

.option-input {
    margin-right: 15px;
    width: 18px;
    height: 18px;
}

.option-label {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.option-price {
    color: #D4AF37;
    font-weight: 600;
}

/* Поля для карты */
.card-fields {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    margin-top: 15px;
    border: 1px solid #e0e0e0;
    display: none;
}

.card-fields.show {
    display: block;
}

.card-row {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 15px;
}

.card-icons {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.card-icon {
    height: 24px;
    opacity: 0.3;
    transition: opacity 0.3s;
}

.card-icon.active {
    opacity: 1;
}

/* Итоговая информация */
.order-summary {
    position: sticky;
    top: 20px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.summary-item.total {
    border-top: 2px solid #eee;
    margin-top: 10px;
    padding-top: 20px;
    font-size: 18px;
    font-weight: 600;
    color: #8B4513;
}

.summary-item.discount {
    color: #28a745;
}

.summary-item.shipping-free {
    color: #28a745;
}

/* Кнопки */
.btn-primary {
    display: block;
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 20px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #A0522D 0%, #D4AF37 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(139, 69, 19, 0.2);
}

.btn-secondary {
    display: block;
    width: 100%;
    padding: 12px;
    background: #f8f9fa;
    color: #666;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-align: center;
    text-decoration: none;
    font-size: 14px;
    margin-top: 10px;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: #e9ecef;
}

/* Гарантии */
.guarantee-box {
    background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);
    border-radius: 15px;
    padding: 20px;
    margin-top: 20px;
    border: 1px solid #e8e8e8;
}

.guarantee-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    font-size: 14px;
    color: #666;
}

/* Адаптивность */
@media (max-width: 992px) {
    .checkout-container {
        grid-template-columns: 1fr;
    }
    
    .order-summary {
        position: static;
    }
}

@media (max-width: 768px) {
    .form-row, .card-row {
        grid-template-columns: 1fr;
    }
    
    .checkout-card {
        padding: 20px;
    }
}
</style>

<div class="checkout-section">
    <h1 class="checkout-title">Оформление заказа</h1>
    
    <?php if(isset($success_message) && $order_completed): ?>
        <div class="order-success">
            <div class="success-icon">✅</div>
            <h2 style="color: #8B4513; margin-bottom: 15px; font-weight: 400;">Заказ успешно оформлен!</h2>
            <p style="color: #666; margin-bottom: 20px;">Ваш заказ принят в обработку. В ближайшее время с вами свяжется наш менеджер для подтверждения.</p>
            
            <div class="order-number">
                № <?php echo $order_details['number']; ?>
            </div>
            
            <div style="margin: 30px 0; padding: 20px; background: #f9f9f9; border-radius: 10px;">
                <div style="font-size: 28px; color: #D4AF37; font-weight: 600;">
                    <?php echo number_format($order_details['total'], 0, ',', ' '); ?> ₽
                </div>
                <p style="color: #666; margin-top: 10px;">Сумма к оплате</p>
            </div>
            
            <p style="color: #666; margin-bottom: 30px;">
                Подтверждение заказа отправлено на email: <strong><?php echo $order_details['email']; ?></strong>
            </p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="cabinet.php" class="btn-primary" style="width: auto; padding: 12px 30px;">
                    Перейти в личный кабинет
                </a>
                <a href="catalog.php" class="btn-secondary" style="width: auto; padding: 12px 30px;">
                    Вернуться в каталог
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="checkout-container">
            <!-- Левая колонка: Форма оформления -->
            <div>
                <?php if(isset($error_message)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        ❌ <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="checkout-card">
                    <h3 class="checkout-card-title">Контактные данные</h3>
                    
                    <form method="POST" action="" id="orderForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Имя и фамилия</label>
                                <input type="text" name="name" class="form-input" required 
                                       value="<?php echo htmlspecialchars($user['username_luxury']); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">Телефон</label>
                                <input type="tel" name="phone" class="form-input" required 
                                       value="<?php echo htmlspecialchars($user['phone_luxury']); ?>"
                                       placeholder="+7 (999) 999-99-99">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-input" required 
                                   value="<?php echo htmlspecialchars($user['email_luxury']); ?>">
                            <small style="color: #666; font-size: 12px;">На этот адрес будет отправлено подтверждение заказа</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">Адрес доставки</label>
                            <textarea name="address" class="form-textarea" required><?php echo htmlspecialchars($user['address_luxury']); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Комментарий к заказу</label>
                            <textarea name="comment" class="form-textarea" placeholder="Дополнительные пожелания"></textarea>
                        </div>
                    </form>
                </div>
                
                <div class="checkout-card">
                    <h3 class="checkout-card-title">Способ доставки</h3>
                    
                    <div class="option-group">
                        <label class="option-item">
                            <input type="radio" name="delivery_method" value="courier" class="option-input" checked required>
                            <div class="option-label">
                                <div>
                                    <div style="font-weight: 500;">Курьерская доставка</div>
                                    <div style="font-size: 13px; color: #666;">Доставка курьером по адресу</div>
                                </div>
                                <div class="option-price">
                                    <?php echo $shipping_cost == 0 ? 'Бесплатно' : '1,000 ₽'; ?>
                                </div>
                            </div>
                        </label>
                        
                        <label class="option-item">
                            <input type="radio" name="delivery_method" value="pickup" class="option-input" required>
                            <div class="option-label">
                                <div>
                                    <div style="font-weight: 500;">Самовывоз из бутика</div>
                                    <div style="font-size: 13px; color: #666;">Москва, ул. Тверская, 10</div>
                                </div>
                                <div class="option-price">Бесплатно</div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="checkout-card">
                    <h3 class="checkout-card-title">Способ оплаты</h3>
                    
                    <div class="option-group">
                        <label class="option-item selected" id="card-option">
                            <input type="radio" name="payment_method" value="card" class="option-input" checked required>
                            <div class="option-label">
                                <div style="font-weight: 500;">Банковской картой онлайн</div>
                            </div>
                        </label>
                        
                        <label class="option-item">
                            <input type="radio" name="payment_method" value="cash" class="option-input" required>
                            <div class="option-label">
                                <div style="font-weight: 500;">Наличными при получении</div>
                            </div>
                        </label>
                        
                        <label class="option-item">
                            <input type="radio" name="payment_method" value="installment" class="option-input" required>
                            <div class="option-label">
                                <div style="font-weight: 500;">Рассрочка 0%</div>
                                <div style="font-size: 13px; color: #666;">На 6 месяцев</div>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Поля для банковской карты -->
                    <div id="card-fields" class="card-fields show">
                        <div class="form-group">
                            <label class="form-label required">Номер карты</label>
                            <input type="text" name="card_number" class="form-input" 
                                   placeholder="1234 5678 9012 3456" maxlength="19">
                            <div class="card-icons">
                                <img src="https://img.icons8.com/color/48/000000/visa.png" alt="Visa" class="card-icon active" id="visa-icon">
                                <img src="https://img.icons8.com/color/48/000000/mastercard.png" alt="Mastercard" class="card-icon" id="mastercard-icon">
                                <img src="https://img.icons8.com/color/48/000000/maestro.png" alt="Maestro" class="card-icon" id="maestro-icon">
                            </div>
                        </div>
                        
                        <div class="card-row">
                            <div class="form-group">
                                <label class="form-label required">Срок действия (ММ/ГГ)</label>
                                <input type="text" name="card_expiry" class="form-input" 
                                       placeholder="ММ/ГГ" maxlength="5">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label required">CVC</label>
                                <input type="text" name="card_cvc" class="form-input" 
                                       placeholder="123" maxlength="4">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required">Имя владельца карты</label>
                            <input type="text" name="card_holder" class="form-input" 
                                   placeholder="IVAN IVANOV">
                        </div>
                    </div>
                </div>
                
                <div class="checkout-card">
                    <h3 class="checkout-card-title">Дополнительные услуги</h3>
                    
                    <label style="display: flex; align-items: center; gap: 10px; padding: 15px; background: #f9f9f9; border-radius: 10px; cursor: pointer;">
                        <input type="checkbox" name="gift_wrapping" style="width: 18px; height: 18px;">
                        <div>
                            <div style="font-weight: 500; color: #333;">Подарочная упаковка</div>
                            <div style="font-size: 13px; color: #666;">Элегантная упаковка в фирменную коробку</div>
                        </div>
                        <div style="margin-left: auto; color: #D4AF37; font-weight: 600;">500 ₽</div>
                    </label>
                    
                    <div class="form-group" style="margin-top: 20px;">
                        <label class="form-label">Промокод</label>
                        <input type="text" name="promo_code" class="form-input" placeholder="Введите промокод">
                    </div>
                </div>
                
                <div class="guarantee-box">
                    <div class="guarantee-item">
                        <span style="color: #28a745;">✓</span>
                        <span>Гарантия подлинности 2 года</span>
                    </div>
                    <div class="guarantee-item">
                        <span style="color: #28a745;">✓</span>
                        <span>Возврат в течение 14 дней</span>
                    </div>
                    <div class="guarantee-item">
                        <span style="color: #28a745;">✓</span>
                        <span>Конфиденциальность данных</span>
                    </div>
                    <div class="guarantee-item">
                        <span style="color: #28a745;">✓</span>
                        <span>Бесплатная экспертиза</span>
                    </div>
                </div>
            </div>
            
            <!-- Правая колонка: Итоговая информация -->
            <div class="order-summary">
                <div class="checkout-card">
                    <h3 class="checkout-card-title">Ваш заказ</h3>
                    
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                        <?php foreach ($cart_items as $item): ?>
                            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #eee;">
                                <div>
                                    <div style="font-weight: 500; color: #333;"><?php echo htmlspecialchars($item['name_luxury']); ?></div>
                                    <div style="font-size: 13px; color: #666;">
                                        <?php echo $item['quantity_luxury']; ?> шт. × <?php echo number_format($item['price_luxury'], 0, ',', ' '); ?> ₽
                                        <?php if($item['material_luxury']): ?>
                                            <br><small><?php echo htmlspecialchars($item['material_luxury']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="font-weight: 600; color: #D4AF37;">
                                    <?php echo number_format($item['price_luxury'] * $item['quantity_luxury'], 0, ',', ' '); ?> ₽
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-item">
                        <span>Товары:</span>
                        <span><?php echo number_format($total_amount, 0, ',', ' '); ?> ₽</span>
                    </div>
                    
                    <?php if($discount_percent > 0): ?>
                        <div class="summary-item discount">
                            <span>Скидка <?php echo $discount_percent; ?>%:</span>
                            <span>-<?php echo number_format($discount_amount, 0, ',', ' '); ?> ₽</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-item">
                        <span>Подарочная упаковка:</span>
                        <span id="gift-wrapping-price">0 ₽</span>
                    </div>
                    
                    <div class="summary-item <?php echo $shipping_cost == 0 ? 'shipping-free' : ''; ?>">
                        <span>Доставка:</span>
                        <span id="shipping-price">
                            <?php echo $shipping_cost == 0 ? 'Бесплатно' : '1,000 ₽'; ?>
                        </span>
                    </div>
                    
                    <?php if($total_amount < 10000): ?>
                        <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px; margin: 15px 0; font-size: 13px; color: #856404;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span>💡</span>
                                <span>Добавьте ещё <?php echo number_format(10000 - $total_amount, 0, ',', ' '); ?> ₽ для бесплатной доставки</span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="summary-item total">
                        <span>Итого к оплате:</span>
                        <span id="final-total"><?php echo number_format($subtotal + $shipping_cost, 0, ',', ' '); ?> ₽</span>
                    </div>
                    
                    <button type="submit" form="orderForm" class="btn-primary">
                        Подтвердить заказ
                    </button>
                    
                    <a href="cart.php" class="btn-secondary">
                        ← Вернуться в корзину
                    </a>
                    
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                        <p style="font-size: 12px; color: #666; text-align: center;">
                            Нажимая "Подтвердить заказ", вы соглашаетесь с условиями покупки и обработки персональных данных
                        </p>
                    </div>
                </div>
                
                <?php if($is_vip): ?>
                    <div style="background: linear-gradient(135deg, #D4AF37, #FFD700); color: white; padding: 20px; border-radius: 15px; margin-top: 20px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="font-size: 24px;">👑</span>
                            <div>
                                <h4 style="margin: 0; font-weight: 500;">VIP КЛИЕНТ</h4>
                                <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">Вы получаете скидку 10% на все заказы</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Управление способами оплаты
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    const cardFields = document.getElementById('card-fields');
    
    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.value === 'card') {
                cardFields.classList.add('show');
                document.getElementById('card-option').classList.add('selected');
            } else {
                cardFields.classList.remove('show');
                document.getElementById('card-option').classList.remove('selected');
            }
        });
    });
    
    // Управление выделением опций доставки
    const deliveryOptions = document.querySelectorAll('input[name="delivery_method"]');
    const optionItems = document.querySelectorAll('.option-item');
    
    optionItems.forEach(item => {
        const radio = item.querySelector('input[type="radio"]');
        if (radio) {
            radio.addEventListener('change', function() {
                optionItems.forEach(i => i.classList.remove('selected'));
                this.closest('.option-item').classList.add('selected');
                
                // Обновляем стоимость доставки в зависимости от выбора
                if (this.value === 'pickup') {
                    document.getElementById('shipping-price').textContent = 'Бесплатно';
                    updateTotal();
                } else if (this.value === 'courier') {
                    const shippingCost = <?php echo $shipping_cost; ?>;
                    document.getElementById('shipping-price').textContent = 
                        shippingCost === 0 ? 'Бесплатно' : '1,000 ₽';
                    updateTotal();
                }
            });
        }
    });
    
    // Обработка промокода
    const promoInput = document.querySelector('input[name="promo_code"]');
    if (promoInput) {
        promoInput.addEventListener('change', function() {
            // Здесь можно добавить проверку промокода через AJAX
            console.log('Проверка промокода:', this.value);
        });
    }
    
    // Маска для телефона
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
    
    // Маска для номера карты
    const cardNumberInput = document.querySelector('input[name="card_number"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            let value = this.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            this.value = formattedValue.substring(0, 19);
            
            // Определение типа карты
            const visaIcon = document.getElementById('visa-icon');
            const mastercardIcon = document.getElementById('mastercard-icon');
            const maestroIcon = document.getElementById('maestro-icon');
            
            visaIcon.classList.remove('active');
            mastercardIcon.classList.remove('active');
            maestroIcon.classList.remove('active');
            
            if (value.startsWith('4')) {
                visaIcon.classList.add('active');
            } else if (value.startsWith('5')) {
                mastercardIcon.classList.add('active');
            } else if (value.startsWith('6')) {
                maestroIcon.classList.add('active');
            }
        });
    }
    
    // Маска для срока действия карты
    const cardExpiryInput = document.querySelector('input[name="card_expiry"]');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function() {
            let value = this.value.replace(/[^0-9]/gi, '');
            
            if (value.length >= 2) {
                this.value = value.substring(0, 2) + '/' + value.substring(2, 4);
            } else {
                this.value = value;
            }
        });
    }
    
    // Обновление итоговой суммы при изменении подарочной упаковки
    const giftWrappingCheckbox = document.querySelector('input[name="gift_wrapping"]');
    const giftWrappingPrice = document.getElementById('gift-wrapping-price');
    const finalTotal = document.getElementById('final-total');
    
    function updateTotal() {
        const subtotal = <?php echo $subtotal; ?>;
        const shippingCost = document.querySelector('input[name="delivery_method"]:checked').value === 'pickup' ? 0 : <?php echo $shipping_cost; ?>;
        const giftWrappingCost = giftWrappingCheckbox.checked ? 500 : 0;
        
        const total = subtotal + shippingCost + giftWrappingCost;
        
        giftWrappingPrice.textContent = giftWrappingCheckbox.checked ? '500 ₽' : '0 ₽';
        finalTotal.textContent = total.toLocaleString('ru-RU') + ' ₽';
    }
    
    if (giftWrappingCheckbox) {
        giftWrappingCheckbox.addEventListener('change', updateTotal);
    }
    
    // Инициализация суммы
    updateTotal();
    
    // Валидация формы
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Проверка обязательных полей
            const requiredFields = orderForm.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#dc3545';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            // Дополнительная проверка email
            const emailField = orderForm.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    isValid = false;
                    emailField.style.borderColor = '#dc3545';
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля корректно.');
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>