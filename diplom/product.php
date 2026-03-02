<?php
require_once 'config.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: catalog.php');
    exit();
}

try {
    $sql = "SELECT p.*, c.name_luxury as category_name 
            FROM luxury_jewelry p 
            LEFT JOIN luxury_categories c ON p.category_id_luxury = c.id 
            WHERE p.id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header('Location: catalog.php');
        exit();
    }
    
    // Получаем похожие товары
    $sql_similar = "SELECT * FROM luxury_jewelry 
                   WHERE category_id_luxury = ? AND id != ? AND stock_luxury > 0
                   ORDER BY RAND() LIMIT 4";
    $stmt_similar = $pdo->prepare($sql_similar);
    $stmt_similar->execute([$product['category_id_luxury'], $product_id]);
    $similar_products = $stmt_similar->fetchAll();
    
} catch (PDOException $e) {
    die("Ошибка при получении товара: " . $e->getMessage());
}
?>

<?php include 'header.php'; ?>

<style>
    .product-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 30px 20px;
        width: 100%;
    }
    
    .product-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        margin-bottom: 60px;
    }
    
    .similar-products-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }
    
    .similar-product-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
        border: 1px solid #e8e8e8;
        text-align: center;
    }
    
    .similar-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(139, 69, 19, 0.1);
    }
    
    /* Адаптивность */
    @media (max-width: 1024px) {
        .product-grid {
            gap: 40px;
        }
        
        .similar-products-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .product-container {
            padding: 20px 15px;
        }
        
        .product-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .similar-products-grid {
            grid-template-columns: 1fr;
        }
        
        .similar-product-card {
            padding: 15px;
        }
    }
</style>

<div class="product-container">
    <!-- Хлебные крошки -->
    <div style="margin-bottom: 30px;">
        <a href="catalog.php" style="color: <?php echo getSetting('primary_color', '#8B4513'); ?>; text-decoration: none; font-weight: 500;">Каталог</a>
        <span style="color: #A0522D; margin: 0 10px;">›</span>
        <a href="catalog.php?category=<?php echo $product['category_id_luxury']; ?>" 
           style="color: <?php echo getSetting('primary_color', '#8B4513'); ?>; text-decoration: none; font-weight: 500;">
            <?php echo htmlspecialchars($product['category_name']); ?>
        </a>
        <span style="color: #A0522D; margin: 0 10px;">›</span>
        <span style="color: #666;"><?php echo htmlspecialchars($product['name_luxury']); ?></span>
    </div>
    
    <div style="background: <?php echo getSetting('card_color', 'white'); ?>; border-radius: 20px; padding: 40px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
        <div class="product-grid">
            <!-- Изображение изделия -->
            <div>
                <div style="background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 15px; padding: 30px; display: flex; align-items: center; justify-content: center; height: 500px; position: relative; overflow: hidden;">
                    <?php if (!empty($product['image_luxury'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_luxury']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name_luxury']); ?>"
                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    <?php else: ?>
                        <div style="text-align: center; color: #d4b89c;">
                            <span style="font-size: 8rem; opacity: 0.3;">💎</span>
                            <p style="margin-top: 20px; color: #a08267; font-size: 16px;">Изображение изделия</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Бейдж для новинок -->
                    <?php 
                        $days_ago = (time() - strtotime($product['created_at_luxury'])) / (60 * 60 * 24);
                        if ($days_ago < 30): 
                    ?>
                        <div style="position: absolute; top: 20px; left: 20px; background: linear-gradient(135deg, #D4AF37, #FFD700); color: white; padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 600; z-index: 1;">
                            НОВИНКА
                        </div>
                    <?php endif; ?>
                    
                    <!-- Бейдж ограниченный выпуск -->
                    <?php if ($product['stock_luxury'] < 10 && $product['stock_luxury'] > 0): ?>
                        <div style="position: absolute; top: 20px; right: 20px; background: linear-gradient(135deg, #dc3545, #e83e8c); color: white; padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 600; z-index: 1;">
                            ОСТАЛОСЬ <?php echo $product['stock_luxury']; ?> шт.
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Миниатюры (если есть дополнительные изображения) -->
                <div style="display: flex; gap: 15px; margin-top: 20px; overflow-x: auto; padding: 10px 0;">
                    <?php for($i = 1; $i <= 3; $i++): ?>
                        <div style="min-width: 80px; height: 80px; background: #f8f0e3; border-radius: 10px; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid #e8d9c5;">
                            <span style="color: #d4b89c; font-size: 24px;">📸</span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            
            <!-- Информация о изделии -->
            <div>
                <h1 style="color: #333; margin-bottom: 15px; font-weight: 300; letter-spacing: 1px; font-size: 32px; line-height: 1.3;">
                    <?php echo htmlspecialchars($product['name_luxury']); ?>
                </h1>
                
                <div style="margin-bottom: 25px;">
                    <span style="background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 600;">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </span>
                    <?php if($product['material_luxury']): ?>
                        <span style="background-color: #e6f7ff; color: #0066cc; padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 600; margin-left: 10px;">
                            <?php echo htmlspecialchars($product['material_luxury']); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <div style="font-size: 40px; font-weight: 500; color: #D4AF37; margin-bottom: 30px;">
                    <?php echo number_format($product['price_luxury'], 0, ',', ' '); ?> ₽
                </div>
                
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <span style="color: <?php echo ($product['stock_luxury'] > 0) ? '#28a745' : '#dc3545'; ?>; font-weight: 600; font-size: 18px; display: flex; align-items: center; gap: 8px;">
                            <?php if($product['stock_luxury'] > 0): ?>
                                <span style="background: #28a745; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">✓</span>
                                В наличии: <?php echo $product['stock_luxury']; ?> шт.
                            <?php else: ?>
                                <span style="background: #dc3545; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px;">✗</span>
                                Нет в наличии
                            <?php endif; ?>
                        </span>
                        
                        <!-- Избранное -->
                        <button onclick="toggleWishlist(<?php echo $product['id']; ?>)" 
                                style="width: 50px; height: 50px; border-radius: 50%; background: #f8f9fa; border: 1px solid #ddd; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s; font-size: 20px;">
                            <span id="wishlist-icon-<?php echo $product['id']; ?>" style="color: #dc3545;">❤️</span>
                        </button>
                    </div>
                </div>
                
                <!-- Характеристики -->
                <div style="margin-bottom: 35px;">
                    <h3 style="color: #333; margin-bottom: 20px; font-weight: 400; font-size: 20px;">Характеристики</h3>
                    <div style="background: #fcf9f5; padding: 25px; border-radius: 12px; border: 1px solid #f0e6d6;">
                        <?php if($product['material_luxury']): ?>
                            <div style="display: flex; padding: 12px 0; border-bottom: 1px solid #e8d9c5;">
                                <div style="color: #8B4513; font-weight: 500; min-width: 150px;">Материал:</div>
                                <div style="color: #333;"><?php echo htmlspecialchars($product['material_luxury']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($product['gemstones_luxury']): ?>
                            <div style="display: flex; padding: 12px 0; border-bottom: 1px solid #e8d9c5;">
                                <div style="color: #8B4513; font-weight: 500; min-width: 150px;">Камни:</div>
                                <div style="color: #333;"><?php echo htmlspecialchars($product['gemstones_luxury']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if($product['weight_luxury']): ?>
                            <div style="display: flex; padding: 12px 0; border-bottom: 1px solid #e8d9c5;">
                                <div style="color: #8B4513; font-weight: 500; min-width: 150px;">Вес:</div>
                                <div style="color: #333;"><?php echo htmlspecialchars($product['weight_luxury']); ?> г</div>
                            </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; padding: 12px 0;">
                            <div style="color: #8B4513; font-weight: 500; min-width: 150px;">Коллекция:</div>
                            <div style="color: #333;"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Описание -->
                <div style="margin-bottom: 40px;">
                    <h3 style="color: #333; margin-bottom: 20px; font-weight: 400; font-size: 20px;">Описание</h3>
                    <div style="color: #666; line-height: 1.8; font-size: 16px;">
                        <?php echo nl2br(htmlspecialchars($product['description_luxury'])); ?>
                    </div>
                </div>
                
                <!-- Кнопки действий -->
                <?php if($product['stock_luxury'] > 0): ?>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_luxury']); ?>')" 
                                style="padding: 18px 40px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 500; cursor: pointer; flex: 1; min-width: 250px; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 12px;"
                                id="cart-btn-<?php echo $product['id']; ?>">
                            <span id="btn-text-<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i> Добавить в корзину
                            </span>
                            <span id="btn-icon-<?php echo $product['id']; ?>" style="display: none; font-size: 18px;">✓</span>
                        </button>
                        
                        <a href="checkout.php?product=<?php echo $product['id']; ?>" 
                           style="padding: 18px 40px; background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%); color: white; text-decoration: none; border-radius: 10px; font-size: 16px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 12px; flex: 1; min-width: 250px; transition: all 0.3s;">
                            <i class="fas fa-bolt"></i> Быстрый заказ
                        </a>
                    </div>
                    
                    <!-- Услуги -->
                    <div style="margin-top: 30px; padding: 25px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 12px; border: 1px solid #e8d9c5;">
                        <h4 style="color: #8B4513; margin-bottom: 15px; font-weight: 500;">Специальные услуги</h4>
                        <div style="display: flex; gap: 15px;">
                            <label style="display: flex; align-items: center; gap: 8px; color: #666;">
                                <input type="checkbox" style="width: 18px; height: 18px;">
                                <span>Подарочная упаковка</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; color: #666;">
                                <input type="checkbox" style="width: 18px; height: 18px;">
                                <span>Гравировка</span>
                            </label>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="padding: 30px; background: linear-gradient(135deg, #fff8e1 0%, #fff3cd 100%); border-radius: 12px; text-align: center; border: 1px solid #ffeaa7;">
                        <h4 style="color: #8B4513; margin-bottom: 15px; font-weight: 500; font-size: 20px;">Изделие временно отсутствует</h4>
                        <p style="color: #A0522D; margin-bottom: 20px;">Оставьте заявку, и мы уведомим вас о поступлении</p>
                        <form onsubmit="alert('Заявка отправлена! Мы сообщим вам о поступлении изделия.'); return false;">
                            <div style="display: flex; gap: 10px; max-width: 400px; margin: 0 auto;">
                                <input type="email" placeholder="Ваш email" 
                                       style="flex: 1; padding: 12px; border: 1px solid #e8d9c5; border-radius: 5px;">
                                <button type="submit" 
                                        style="padding: 12px 25px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                                    Уведомить
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Похожие изделия -->
        <?php if(!empty($similar_products)): ?>
        <div style="margin-top: 60px; padding-top: 40px; border-top: 1px solid #e8e8e8;">
            <h3 style="color: #333; margin-bottom: 30px; font-weight: 300; letter-spacing: 1px; font-size: 24px; text-align: center;">Похожие изделия</h3>
            <div class="similar-products-grid">
                <?php foreach ($similar_products as $similar): ?>
                    <div class="similar-product-card">
                        <div style="height: 200px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 10px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                            <?php if (!empty($similar['image_luxury'])): ?>
                                <img src="<?php echo htmlspecialchars($similar['image_luxury']); ?>" 
                                     alt="<?php echo htmlspecialchars($similar['name_luxury']); ?>"
                                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <span style="color: #d4b89c; font-size: 3rem; opacity: 0.5;">💎</span>
                            <?php endif; ?>
                        </div>
                        <h4 style="margin: 0 0 10px 0; font-size: 16px; height: 45px; overflow: hidden; line-height: 1.4;">
                            <a href="product.php?id=<?php echo $similar['id']; ?>" 
                               style="color: #333; text-decoration: none; font-weight: 400;">
                                <?php echo htmlspecialchars($similar['name_luxury']); ?>
                            </a>
                        </h4>
                        <div style="font-weight: 600; color: #D4AF37; margin-bottom: 15px; font-size: 18px;">
                            <?php echo number_format($similar['price_luxury'], 0, ',', ' '); ?> ₽
                        </div>
                        <button onclick="addToCart(<?php echo $similar['id']; ?>, '<?php echo htmlspecialchars($similar['name_luxury']); ?>')" 
                                style="width: 100%; padding: 12px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.3s;">
                            В корзину
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function addToCart(productId, productName) {
    const button = document.getElementById(`cart-btn-${productId}`);
    const btnText = document.getElementById(`btn-text-${productId}`);
    const btnIcon = document.getElementById(`btn-icon-${productId}`);
    
    button.classList.add('btn-success');
    btnText.style.display = 'none';
    btnIcon.style.display = 'inline-block';
    btnIcon.style.animation = 'checkmark 0.3s ease-out';
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&action=add'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`${productName} добавлен в корзину!`, 'success');
            updateCartCount(data.cart_count);
            
            setTimeout(() => {
                button.classList.remove('btn-success');
                btnText.style.display = 'inline-block';
                btnIcon.style.display = 'none';
            }, 1500);
        } else {
            btnText.textContent = 'Ошибка';
            btnText.style.color = '#dc3545';
            setTimeout(() => {
                btnText.textContent = 'Добавить в корзину';
                btnText.style.color = 'white';
                btnText.style.display = 'inline-block';
                btnIcon.style.display = 'none';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btnText.textContent = 'Ошибка';
        btnText.style.color = '#dc3545';
        setTimeout(() => {
            btnText.textContent = 'Добавить в корзину';
            btnText.style.color = 'white';
            btnText.style.display = 'inline-block';
            btnIcon.style.display = 'none';
        }, 2000);
    });
}

function toggleWishlist(productId) {
    const icon = document.getElementById(`wishlist-icon-${productId}`);
    const isActive = icon.textContent === '❤️';
    
    fetch('wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&action=' + (isActive ? 'remove' : 'add')
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (isActive) {
                icon.textContent = '🤍';
                showNotification('Удалено из избранного', 'info');
            } else {
                icon.textContent = '❤️';
                showNotification('Добавлено в избранное', 'success');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при изменении избранного', 'error');
    });
}

function showNotification(message, type = 'success') {
    // Создаем или получаем контейнер для уведомлений
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        notificationContainer.style.cssText = 'position: fixed; top: 100px; right: 20px; z-index: 10000;';
        document.body.appendChild(notificationContainer);
    }
    
    const notification = document.createElement('div');
    const colors = {
        'success': 'linear-gradient(135deg, #28a745, #20c997)',
        'error': 'linear-gradient(135deg, #dc3545, #e83e8c)',
        'info': 'linear-gradient(135deg, #17a2b8, #138496)'
    };
    
    notification.innerHTML = `
        <div style="background: ${colors[type]}; color: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: space-between; min-width: 300px; animation: slideIn 0.3s ease-out;">
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" style="background: transparent; border: none; color: white; font-size: 20px; cursor: pointer; margin-left: 15px;">×</button>
        </div>
    `;
    
    notificationContainer.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }
    }, 3000);
}

function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.style.display = count > 0 ? 'inline' : 'none';
        cartCountElement.classList.add('pulse');
        setTimeout(() => {
            cartCountElement.classList.remove('pulse');
        }, 500);
    }
}

// Добавляем стили для анимаций
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    @keyframes checkmark {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); opacity: 1; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
    }
`;
document.head.appendChild(style);
</script>

<?php include 'footer.php'; ?>