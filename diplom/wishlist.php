<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Обработка удаления из избранного
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_wishlist'])) {
    $product_id = intval($_POST['product_id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM luxury_wishlist WHERE user_id_luxury = ? AND product_id_luxury = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $success_message = "Изделие удалено из избранного";
    } catch (PDOException $e) {
        $error_message = "Ошибка при удалении: " . $e->getMessage();
    }
}

// Обработка добавления в корзину из избранного
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    
    try {
        // Проверяем, есть ли уже товар в корзине
        $stmt = $pdo->prepare("SELECT * FROM luxury_cart WHERE user_id_luxury = ? AND product_id_luxury = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $existing_item = $stmt->fetch();
        
        if ($existing_item) {
            // Увеличиваем количество
            $stmt = $pdo->prepare("UPDATE luxury_cart SET quantity_luxury = quantity_luxury + 1 WHERE id = ?");
            $stmt->execute([$existing_item['id']]);
        } else {
            // Добавляем новый товар
            $stmt = $pdo->prepare("INSERT INTO luxury_cart (user_id_luxury, product_id_luxury, quantity_luxury) VALUES (?, ?, 1)");
            $stmt->execute([$_SESSION['user_id'], $product_id]);
        }
        
        $success_message = "Изделие добавлено в корзину!";
        
    } catch (PDOException $e) {
        $error_message = "Ошибка при добавлении в корзину: " . $e->getMessage();
    }
}

// Получаем список избранного
try {
    $sql = "SELECT w.*, p.name_luxury, p.price_luxury, p.image_luxury, p.stock_luxury, 
                   p.material_luxury, p.category_id_luxury, c.name_luxury as category_name
            FROM luxury_wishlist w
            JOIN luxury_jewelry p ON w.product_id_luxury = p.id
            LEFT JOIN luxury_categories c ON p.category_id_luxury = c.id
            WHERE w.user_id_luxury = ?
            ORDER BY w.added_at_luxury DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
    $wishlist_items = $stmt->fetchAll();
    
    // Получаем количество избранных товаров
    $wishlist_count = count($wishlist_items);
    
} catch (PDOException $e) {
    die("Ошибка при получении избранного: " . $e->getMessage());
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 1400px; margin: 0 auto; padding: 40px 20px;">
    <!-- Хлебные крошки -->
    <div style="margin-bottom: 30px;">
        <a href="cabinet.php" style="color: <?php echo getSetting('primary_color', '#8B4513'); ?>; text-decoration: none; font-weight: 500;">Личный кабинет</a>
        <span style="color: #A0522D; margin: 0 10px;">›</span>
        <span style="color: #666;">Избранное</span>
    </div>
    
    <h1 style="color: #8B4513; margin-bottom: 10px; font-weight: 300; letter-spacing: 1px; font-size: 32px;">
        <i class="fas fa-heart" style="color: #dc3545; margin-right: 15px;"></i> 
        Избранные изделия
        <?php if($wishlist_count > 0): ?>
            <span style="font-size: 18px; color: #666; font-weight: normal;"> (<?php echo $wishlist_count; ?> изделий)</span>
        <?php endif; ?>
    </h1>
    
    <p style="color: #666; margin-bottom: 40px; font-size: 16px;">
        Сохраняйте понравившиеся изделия, чтобы не потерять их
    </p>
    
    <!-- Уведомления -->
    <?php if(isset($success_message)): ?>
        <div style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); color: #2e7d32; padding: 15px 25px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #4caf50;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error_message)): ?>
        <div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px 25px; border-radius: 10px; margin-bottom: 30px; border-left: 4px solid #f44336;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Сортировка и фильтры -->
    <?php if($wishlist_count > 0): ?>
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div style="color: #666; font-size: 14px;">
                <i class="fas fa-filter"></i> Сортировка:
                <select style="margin-left: 10px; padding: 8px 15px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; color: #555;">
                    <option>По дате добавления</option>
                    <option>По цене (сначала дорогие)</option>
                    <option>По цене (сначала дешевые)</option>
                    <option>По названию</option>
                </select>
            </div>
            
            <div>
                <button onclick="clearWishlist()" 
                        style="padding: 10px 20px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; border: 1px solid #e8d9c5; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.3s;">
                    <i class="fas fa-trash-alt"></i> Очистить избранное
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Список избранного -->
    <?php if($wishlist_count == 0): ?>
        <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
            <div style="font-size: 80px; color: #ffcdd2; margin-bottom: 20px;">❤️</div>
            <h3 style="color: #666; margin-bottom: 15px; font-weight: 400; font-size: 24px;">Избранное пусто</h3>
            <p style="color: #888; margin-bottom: 30px; font-size: 16px; max-width: 500px; margin-left: auto; margin-right: auto;">
                Добавляйте понравившиеся изделия в избранное, нажимая на сердечко 💖
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="catalog.php" 
                   style="padding: 15px 30px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fas fa-gem"></i> Перейти в каталог
                </a>
                <a href="collections.php" 
                   style="padding: 15px 30px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; text-decoration: none; border-radius: 8px; font-weight: 500; border: 1px solid #e8d9c5; display: inline-flex; align-items: center; gap: 10px;">
                    <i class="fas fa-crown"></i> Посмотреть коллекции
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="wishlist-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
            <?php foreach ($wishlist_items as $item): ?>
                <div class="wishlist-item" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8; transition: transform 0.3s; position: relative;">
                    <!-- Удалить из избранного -->
                    <form method="POST" action="" style="position: absolute; top: 15px; right: 15px; z-index: 2;">
                        <input type="hidden" name="product_id" value="<?php echo $item['product_id_luxury']; ?>">
                        <button type="submit" name="remove_from_wishlist" 
                                style="width: 40px; height: 40px; background: rgba(255, 255, 255, 0.9); border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #dc3545; font-size: 18px; transition: all 0.3s; backdrop-filter: blur(5px);"
                                onmouseover="this.style.background='rgba(220, 53, 69, 0.1)'; this.style.transform='scale(1.1)'"
                                onmouseout="this.style.background='rgba(255, 255, 255, 0.9)'; this.style.transform='scale(1)'">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                    
                    <!-- Ссылка на товар -->
                    <a href="product.php?id=<?php echo $item['product_id_luxury']; ?>" style="text-decoration: none; color: inherit; display: block;">
                        <!-- Изображение -->
                        <div style="height: 250px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                            <?php if(!empty($item['image_luxury'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_luxury']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name_luxury']); ?>"
                                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <span style="color: #d4b89c; font-size: 4rem; opacity: 0.5;">💎</span>
                            <?php endif; ?>
                            
                            <!-- Статус наличия -->
                            <div style="position: absolute; bottom: 15px; left: 15px;">
                                <span style="background: <?php echo $item['stock_luxury'] > 0 ? '#28a745' : '#dc3545'; ?>; color: white; padding: 6px 15px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                    <?php echo $item['stock_luxury'] > 0 ? 'В наличии' : 'Нет в наличии'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Информация -->
                        <div style="padding: 25px;">
                            <h4 style="margin: 0 0 10px 0; color: #333; font-size: 18px; font-weight: 400; line-height: 1.4;">
                                <?php echo htmlspecialchars($item['name_luxury']); ?>
                            </h4>
                            
                            <!-- Категория и материал -->
                            <div style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 8px;">
                                <span style="background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?php echo htmlspecialchars($item['category_name']); ?>
                                </span>
                                <?php if($item['material_luxury']): ?>
                                    <span style="background-color: #e6f7ff; color: #0066cc; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        <?php echo htmlspecialchars($item['material_luxury']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Цена -->
                            <div style="font-size: 24px; font-weight: 500; color: #D4AF37; margin-bottom: 20px;">
                                <?php echo number_format($item['price_luxury'], 0, ',', ' '); ?> ₽
                            </div>
                            
                            <!-- Дата добавления -->
                            <div style="color: #888; font-size: 13px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                                <i class="far fa-clock"></i> Добавлено: <?php echo date('d.m.Y', strtotime($item['added_at_luxury'])); ?>
                            </div>
                        </div>
                    </a>
                    
                    <!-- Кнопки действий -->
                    <div style="padding: 0 25px 25px 25px;">
                        <div style="display: flex; gap: 10px;">
                            <?php if($item['stock_luxury'] > 0): ?>
                                <form method="POST" action="" style="flex: 1;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id_luxury']; ?>">
                                    <button type="submit" name="add_to_cart" 
                                            style="width: 100%; padding: 12px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px;">
                                        <i class="fas fa-shopping-cart"></i> В корзину
                                    </button>
                                </form>
                                
                                <a href="product.php?id=<?php echo $item['product_id_luxury']; ?>" 
                                   style="flex: 1; padding: 12px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid #e8d9c5; transition: all 0.3s;">
                                    <i class="fas fa-eye"></i> Подробнее
                                </a>
                            <?php else: ?>
                                <button style="width: 100%; padding: 12px; background: #f8f9fa; color: #666; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: not-allowed;">
                                    <i class="fas fa-bell"></i> Уведомить о поступлении
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Рекомендации -->
        <?php
        try {
            if($wishlist_count > 0) {
                // Получаем ID категорий из избранного
                $category_ids = array_column($wishlist_items, 'category_id_luxury');
                $category_ids = array_unique($category_ids);
                
                if(!empty($category_ids)) {
                    $placeholders = str_repeat('?,', count($category_ids) - 1) . '?';
                    
                    // Получаем похожие товары
                    $sql = "SELECT p.*, c.name_luxury as category_name 
                            FROM luxury_jewelry p
                            LEFT JOIN luxury_categories c ON p.category_id_luxury = c.id
                            WHERE p.category_id_luxury IN ($placeholders) 
                            AND p.stock_luxury > 0 
                            AND p.id NOT IN (SELECT product_id_luxury FROM luxury_wishlist WHERE user_id_luxury = ?)
                            ORDER BY RAND() 
                            LIMIT 6";
                    
                    $params = array_merge($category_ids, [$_SESSION['user_id']]);
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $recommended = $stmt->fetchAll();
                    
                    if(!empty($recommended)): ?>
                    <div style="margin-top: 80px; padding-top: 40px; border-top: 1px solid #e8e8e8;">
                        <h3 style="color: #8B4513; margin-bottom: 30px; font-weight: 300; letter-spacing: 1px; font-size: 24px;">
                            <i class="fas fa-star" style="color: #D4AF37; margin-right: 10px;"></i> 
                            Вам также может понравиться
                        </h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px;">
                            <?php foreach ($recommended as $product): ?>
                                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border: 1px solid #e8e8e8; transition: transform 0.3s;">
                                    <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration: none; color: inherit;">
                                        <div style="height: 180px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); display: flex; align-items: center; justify-content: center;">
                                            <?php if(!empty($product['image_luxury'])): ?>
                                                <img src="<?php echo htmlspecialchars($product['image_luxury']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name_luxury']); ?>"
                                                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            <?php else: ?>
                                                <span style="color: #d4b89c; font-size: 3rem; opacity: 0.5;">💎</span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="padding: 15px;">
                                            <h5 style="margin: 0 0 10px 0; color: #333; font-size: 15px; font-weight: 400; height: 40px; overflow: hidden;">
                                                <?php echo htmlspecialchars($product['name_luxury']); ?>
                                            </h5>
                                            <div style="font-weight: 600; color: #D4AF37; font-size: 16px;">
                                                <?php echo number_format($product['price_luxury'], 0, ',', ' '); ?> ₽
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif;
                }
            }
        } catch (PDOException $e) {
            // Игнорируем ошибки рекомендаций
        }
        ?>
        
        <!-- Быстрые действия -->
        <div style="margin-top: 50px; padding: 30px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 15px; border: 1px solid #e8d9c5;">
            <h3 style="color: #8B4513; margin-bottom: 20px; font-weight: 400; font-size: 20px; display: flex; align-items: center; gap: 12px;">
                <i class="fas fa-bolt" style="color: #D4AF37;"></i> Быстрые действия
            </h3>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button onclick="addAllToCart()" 
                        style="padding: 15px 30px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-cart-plus"></i> Добавить все в корзину
                </button>
                <button onclick="shareWishlist()" 
                        style="padding: 15px 30px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; border: 1px solid #e8d9c5; border-radius: 8px; cursor: pointer; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-share-alt"></i> Поделиться списком
                </button>
                <a href="catalog.php" 
                   style="padding: 15px 30px; background: white; color: #666; text-decoration: none; border-radius: 8px; font-size: 15px; font-weight: 500; display: flex; align-items: center; gap: 10px; border: 1px solid #ddd;">
                    <i class="fas fa-plus"></i> Добавить ещё изделий
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Очистка всего избранного
function clearWishlist() {
    if(confirm('Вы уверены, что хотите очистить всё избранное?')) {
        fetch('clear_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showNotification('Избранное очищено!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification('Ошибка при очистке избранного', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при очистке избранного', 'error');
        });
    }
}

// Добавление всех товаров в корзину
function addAllToCart() {
    if(confirm('Добавить все товары из избранного в корзину?')) {
        fetch('add_all_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showNotification(data.message, 'success');
                updateCartCount(data.cart_count);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при добавлении товаров', 'error');
        });
    }
}

// Поделиться списком
function shareWishlist() {
    const shareText = 'Мой список избранного в Luxury Jewelry:\n';
    const productList = Array.from(document.querySelectorAll('.wishlist-item h4'))
        .map(h4 => '• ' + h4.textContent)
        .join('\n');
    
    const fullText = shareText + productList + '\n\nПосмотреть коллекцию: ' + window.location.origin;
    
    if(navigator.share) {
        navigator.share({
            title: 'Мой список избранного - Luxury Jewelry',
            text: fullText,
            url: window.location.href
        });
    } else {
        // Копирование в буфер обмена
        navigator.clipboard.writeText(fullText).then(() => {
            showNotification('Список скопирован в буфер обмена!', 'success');
        });
    }
}

// Показать уведомление
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const colors = {
        'success': 'linear-gradient(135deg, #28a745, #20c997)',
        'error': 'linear-gradient(135deg, #dc3545, #e83e8c)',
        'info': 'linear-gradient(135deg, #17a2b8, #138496)'
    };
    
    notification.innerHTML = `
        <div style="position: fixed; top: 100px; right: 20px; background: ${colors[type]}; color: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 10000; display: flex; align-items: center; justify-content: space-between; min-width: 300px; animation: slideIn 0.3s ease-out;">
            <span>${message}</span>
            <button onclick="this.parentElement.remove()" style="background: transparent; border: none; color: white; font-size: 20px; cursor: pointer; margin-left: 15px;">×</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
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

// Анимация карточек
document.addEventListener('DOMContentLoaded', function() {
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    wishlistItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 15px 35px rgba(0,0,0,0.1)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 25px rgba(0,0,0,0.05)';
        });
    });
    
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
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
});

// Адаптивность
window.addEventListener('resize', function() {
    const grid = document.querySelector('.wishlist-grid');
    if (window.innerWidth < 768) {
        grid.style.gridTemplateColumns = '1fr';
    } else {
        grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(350px, 1fr))';
    }
});
</script>

<?php include 'footer.php'; ?>