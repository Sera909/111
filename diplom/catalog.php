<?php
require_once 'config.php';

// Получаем категории ювелирных изделий
try {
    $stmt = $pdo->query("SELECT * FROM luxury_categories ORDER BY name_luxury");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Ошибка при получении категорий: " . $e->getMessage());
}

// Фильтрация по категории
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$material = isset($_GET['material']) ? trim($_GET['material']) : '';
$price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
$price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 0;

// Формируем запрос изделий
$sql = "SELECT p.*, c.name_luxury as category_name 
        FROM luxury_jewelry p 
        LEFT JOIN luxury_categories c ON p.category_id_luxury = c.id 
        WHERE 1=1 AND p.stock_luxury > 0";

$params = [];

if ($category_id > 0) {
    $sql .= " AND p.category_id_luxury = ?";
    $params[] = $category_id;
}

if (!empty($search)) {
    $sql .= " AND (p.name_luxury LIKE ? OR p.description_luxury LIKE ? OR p.gemstones_luxury LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($material)) {
    $sql .= " AND p.material_luxury = ?";
    $params[] = $material;
}

if ($price_min > 0) {
    $sql .= " AND p.price_luxury >= ?";
    $params[] = $price_min;
}

if ($price_max > 0) {
    $sql .= " AND p.price_luxury <= ?";
    $params[] = $price_max;
}

$sql .= " ORDER BY p.created_at_luxury DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Ошибка при получении изделий: " . $e->getMessage());
}

// Материалы для фильтра
$materials = ['золото', 'серебро', 'платина', 'белое золото', 'розовое золото'];
?>

<?php include 'header.php'; ?>

<div style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #8B4513; margin-bottom: 30px; font-weight: 300; letter-spacing: 1px;">Коллекция ювелирных изделий</h1>
    
    <!-- Уведомление о добавлении в корзину -->
    <div id="cart-notification" style="display: none; position: fixed; top: 100px; right: 20px; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 20px; border-radius: 5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; animation: slideIn 0.3s ease-out;">
        <span id="notification-message">Изделие добавлено в корзину!</span>
        <span id="notification-close" style="margin-left: 15px; cursor: pointer; font-weight: bold;">×</span>
    </div>
    
    <!-- Поиск и фильтры -->
    <div style="background: white; padding: 25px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
        <form method="GET" action="" id="filter-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px; font-weight: 500;">Поиск изделий</label>
                <input type="text" name="search" placeholder="Название или описание..." 
                       value="<?php echo htmlspecialchars($search); ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px; font-weight: 500;">Категория</label>
                <select name="category" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                    <option value="0">Все категории</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name_luxury']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px; font-weight: 500;">Материал</label>
                <select name="material" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
                    <option value="">Любой материал</option>
                    <?php foreach ($materials as $mat): ?>
                        <option value="<?php echo $mat; ?>" 
                                <?php echo ($material == $mat) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($mat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px; font-weight: 500;">Цена от</label>
                <input type="number" name="price_min" placeholder="Мин. цена" min="0"
                       value="<?php echo $price_min; ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 5px; color: #555; font-size: 14px; font-weight: 500;">Цена до</label>
                <input type="number" name="price_max" placeholder="Макс. цена" min="0"
                       value="<?php echo $price_max; ?>"
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" 
                        style="flex: 1; padding: 12px 20px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; transition: all 0.3s;">
                    Найти
                </button>
                
                <a href="catalog.php" 
                   style="flex: 1; padding: 12px 20px; background: #f8f9fa; color: #666; text-decoration: none; border-radius: 5px; text-align: center; border: 1px solid #ddd; transition: all 0.3s; display: flex; align-items: center; justify-content: center;">
                    Сбросить
                </a>
            </div>
        </form>
    </div>
    
    <!-- Быстрые категории -->
    <div style="margin-bottom: 30px;">
        <h3 style="color: #8B4513; margin-bottom: 15px; font-weight: 400;">Категории изделий</h3>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            <a href="catalog.php" 
               style="padding: 10px 20px; background: <?php echo ($category_id == 0) ? 'linear-gradient(135deg, #8B4513 0%, #A0522D 100%)' : '#f8f9fa'; ?>; 
                      color: <?php echo ($category_id == 0) ? 'white' : '#666'; ?>; 
                      text-decoration: none; border-radius: 25px; transition: all 0.3s; border: 1px solid <?php echo ($category_id == 0) ? 'transparent' : '#ddd'; ?>;">
                Все изделия
            </a>
            <?php foreach ($categories as $category): ?>
                <a href="catalog.php?category=<?php echo $category['id']; ?>" 
                   style="padding: 10px 20px; background: <?php echo ($category_id == $category['id']) ? 'linear-gradient(135deg, #8B4513 0%, #A0522D 100%)' : '#f8f9fa'; ?>; 
                          color: <?php echo ($category_id == $category['id']) ? 'white' : '#666'; ?>; 
                          text-decoration: none; border-radius: 25px; transition: all 0.3s; border: 1px solid <?php echo ($category_id == $category['id']) ? 'transparent' : '#ddd'; ?>;">
                    <?php echo htmlspecialchars($category['name_luxury']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Результаты поиска -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: #8B4513; margin: 0; font-weight: 400;">
                <?php if ($category_id > 0): ?>
                    <?php 
                        $category_name = '';
                        foreach ($categories as $cat) {
                            if ($cat['id'] == $category_id) {
                                $category_name = $cat['name_luxury'];
                                break;
                            }
                        }
                        echo htmlspecialchars($category_name);
                    ?>
                <?php elseif (!empty($search)): ?>
                    Результаты поиска: "<?php echo htmlspecialchars($search); ?>"
                <?php elseif (!empty($material)): ?>
                    Изделия из <?php echo htmlspecialchars($material); ?>
                <?php else: ?>
                    Все ювелирные изделия
                <?php endif; ?>
                <span style="font-size: 14px; color: #666; font-weight: normal;"> (<?php echo count($products); ?> изделий)</span>
            </h3>
            
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="color: #666; font-size: 14px;">Сортировка:</span>
                <select style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; color: #555;">
                    <option>По популярности</option>
                    <option>По возрастанию цены</option>
                    <option>По убыванию цены</option>
                    <option>По новизне</option>
                </select>
            </div>
        </div>
        
        <?php if (empty($products)): ?>
            <div style="text-align: center; padding: 60px; background: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0,0,0,0.05); border: 1px solid #e8e8e8;">
                <div style="font-size: 5rem; margin-bottom: 20px; opacity: 0.3;">💎</div>
                <p style="color: #666; font-size: 18px; margin-bottom: 10px;">Изделия не найдены</p>
                <p style="color: #888; font-size: 14px;">Попробуйте изменить параметры поиска</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                <?php foreach ($products as $product): ?>
                    <div id="product-<?php echo $product['id']; ?>" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.05); transition: transform 0.3s, box-shadow 0.3s; border: 1px solid #e8e8e8; position: relative;">
                        
                        <!-- Бейдж "Новинка" для свежих товаров -->
                        <?php 
                            $days_ago = (time() - strtotime($product['created_at_luxury'])) / (60 * 60 * 24);
                            if ($days_ago < 30): 
                        ?>
                            <div style="position: absolute; top: 15px; left: 15px; background: linear-gradient(135deg, #D4AF37, #FFD700); color: white; padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; z-index: 1;">
                                НОВИНКА
                            </div>
                        <?php endif; ?>
                        
                        <!-- Бейдж ограниченный выпуск -->
                        <?php if ($product['stock_luxury'] < 10 && $product['stock_luxury'] > 0): ?>
                            <div style="position: absolute; top: 15px; right: 15px; background: linear-gradient(135deg, #dc3545, #e83e8c); color: white; padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; z-index: 1;">
                                ОСТАЛОСЬ <?php echo $product['stock_luxury']; ?> шт.
                            </div>
                        <?php endif; ?>
                        
                        <div style="height: 250px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                            <?php if (!empty($product['image_luxury'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image_luxury']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name_luxury']); ?>"
                                     style="max-width: 100%; max-height: 100%; object-fit: cover; transition: transform 0.5s;">
                            <?php else: ?>
                                <span style="color: #d4b89c; font-size: 5rem; opacity: 0.5;">💎</span>
                            <?php endif; ?>
                            
                            <!-- Кнопка быстрого просмотра -->
                            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0); opacity: 0; transition: all 0.3s; z-index: 2;">
                                <button onclick="quickView(<?php echo $product['id']; ?>)" 
                                        style="padding: 12px 24px; background: rgba(139, 69, 19, 0.9); color: white; border: none; border-radius: 25px; cursor: pointer; font-size: 14px; font-weight: 500; backdrop-filter: blur(5px);">
                                    Быстрый просмотр
                                </button>
                            </div>
                        </div>
                        
                        <div style="padding: 25px;">
                            <h4 style="margin: 0 0 10px 0; color: #333; font-size: 18px; font-weight: 400; min-height: 54px;">
                                <?php echo htmlspecialchars($product['name_luxury']); ?>
                            </h4>
                            
                            <div style="margin-bottom: 15px;">
                                <span style="background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    <?php echo htmlspecialchars($product['category_name']); ?>
                                </span>
                                <?php if($product['material_luxury']): ?>
                                    <span style="background-color: #e6f7ff; color: #0066cc; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; margin-left: 5px;">
                                        <?php echo htmlspecialchars($product['material_luxury']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <p style="color: #666; font-size: 14px; margin-bottom: 20px; min-height: 60px; line-height: 1.5;">
                                <?php 
                                    $description = $product['description_luxury'];
                                    if (strlen($description) > 80) {
                                        echo htmlspecialchars(substr($description, 0, 80)) . '...';
                                    } else {
                                        echo htmlspecialchars($description);
                                    }
                                ?>
                            </p>
                            
                            <?php if($product['gemstones_luxury']): ?>
                                <div style="color: #888; font-size: 13px; margin-bottom: 15px; display: flex; align-items: center; gap: 5px;">
                                    <span>💎</span> <?php echo htmlspecialchars($product['gemstones_luxury']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <div>
                                    <div style="font-size: 24px; font-weight: 500; color: #D4AF37;">
                                        <?php echo number_format($product['price_luxury'], 0, ',', ' '); ?> ₽
                                    </div>
                                    <div style="font-size: 12px; color: <?php echo ($product['stock_luxury'] > 0) ? '#28a745' : '#dc3545'; ?>; display: flex; align-items: center; gap: 5px;">
                                        <?php if($product['stock_luxury'] > 0): ?>
                                            <span>✓</span> В наличии
                                        <?php else: ?>
                                            <span>✗</span> Нет в наличии
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Кнопка избранного -->
                                <button onclick="toggleWishlist(<?php echo $product['id']; ?>)" 
                                        style="width: 40px; height: 40px; border-radius: 50%; background: #f8f9fa; border: 1px solid #ddd; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                    <span id="wishlist-icon-<?php echo $product['id']; ?>" style="font-size: 18px; color: #dc3545;">❤️</span>
                                </button>
                            </div>
                            
                            <div style="display: flex; gap: 10px;">
                                <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_luxury']); ?>')" 
                                        id="cart-btn-<?php echo $product['id']; ?>"
                                        style="flex: 1; padding: 14px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 14px; font-weight: 500;"
                                        <?php echo ($product['stock_luxury'] <= 0) ? 'disabled style="background: #ccc; cursor: not-allowed;"' : ''; ?>>
                                    <span id="btn-text-<?php echo $product['id']; ?>">
                                        <?php echo ($product['stock_luxury'] > 0) ? 'В корзину' : 'Нет в наличии'; ?>
                                    </span>
                                    <span id="btn-icon-<?php echo $product['id']; ?>" style="display: none; font-size: 16px;">✓</span>
                                </button>
                                
                                <a href="product.php?id=<?php echo $product['id']; ?>" 
                                   style="padding: 14px 20px; background-color: #f8f9fa; color: #666; text-decoration: none; border-radius: 8px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; transition: all 0.3s;">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Пагинация -->
            <?php if(count($products) > 12): ?>
                <div style="margin-top: 40px; display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <a href="#" style="padding: 10px 15px; background: #f8f9fa; color: #666; text-decoration: none; border-radius: 5px; border: 1px solid #ddd;">←</a>
                    <a href="#" style="padding: 10px 15px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 5px;">1</a>
                    <a href="#" style="padding: 10px 15px; background: #f8f9fa; color: #666; text-decoration: none; border-radius: 5px; border: 1px solid #ddd;">2</a>
                    <a href="#" style="padding: 10px 15px; background: #f8f9fa; color: #666; text-decoration: none; border-radius: 5px; border: 1px solid #ddd;">3</a>
                    <span style="color: #666;">...</span>
                    <a href="#" style="padding: 10px 15px; background: #f8f9fa; color: #666; text-decoration: none; border-radius: 5px; border: 1px solid #ddd;">→</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <!-- Преимущества -->
    <div style="margin-top: 60px; padding: 40px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 20px; border: 1px solid #e8e8e8;">
        <h3 style="color: #8B4513; text-align: center; margin-bottom: 30px; font-weight: 400;">Почему выбирают Luxury Jewelry</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
            <div style="text-align: center;">
                <div style="font-size: 40px; margin-bottom: 15px;">💎</div>
                <h4 style="color: #333; margin-bottom: 10px; font-weight: 500;">Подлинность</h4>
                <p style="color: #666; font-size: 14px;">Все изделия имеют сертификаты подлинности</p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 40px; margin-bottom: 15px;">🛡️</div>
                <h4 style="color: #333; margin-bottom: 10px; font-weight: 500;">Гарантия 2 года</h4>
                <p style="color: #666; font-size: 14px;">Расширенная гарантия на все ювелирные изделия</p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 40px; margin-bottom: 15px;">🚚</div>
                <h4 style="color: #333; margin-bottom: 10px; font-weight: 500;">Бесплатная доставка</h4>
                <p style="color: #666; font-size: 14px;">При заказе от 10,000 ₽ — доставка бесплатно</p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 40px; margin-bottom: 15px;">🎁</div>
                <h4 style="color: #333; margin-bottom: 10px; font-weight: 500;">Подарочная упаковка</h4>
                <p style="color: #666; font-size: 14px;">Бесплатная подарочная упаковка по запросу</p>
            </div>
        </div>
    </div>
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

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 5px 20px rgba(139, 69, 19, 0.1);
    }
    50% {
        transform: scale(1.02);
        box-shadow: 0 10px 30px rgba(139, 69, 19, 0.2);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 5px 20px rgba(139, 69, 19, 0.1);
    }
}

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
        opacity: 1;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.product-added {
    animation: pulse 0.5s ease;
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

/* Стили для ховера на карточке */
#product-<?php echo $product['id']; ?>:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

#product-<?php echo $product['id']; ?>:hover div[style*="height: 250px"] img {
    transform: scale(1.05);
}

#product-<?php echo $product['id']; ?>:hover div[style*="position: absolute; top: 50%; left: 50%"] {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

/* Стили для модального окна быстрого просмотра */
.quick-view-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 1001;
    justify-content: center;
    align-items: center;
}

.quick-view-content {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Адаптивность */
@media (max-width: 768px) {
    form[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr))"] {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
    }
    
    div[style*="padding: 40px"] {
        padding: 25px !important;
    }
}
</style>

<script>
let notificationTimeout;
let currentProductName = '';

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

function addToCart(productId, productName) {
    if (currentProductName === productName) return;
    
    currentProductName = productName;
    const button = document.getElementById(`cart-btn-${productId}`);
    const btnText = document.getElementById(`btn-text-${productId}`);
    const btnIcon = document.getElementById(`btn-icon-${productId}`);
    const productCard = document.getElementById(`product-${productId}`);
    
    button.classList.add('btn-success');
    btnText.style.display = 'none';
    btnIcon.style.display = 'inline-block';
    btnIcon.style.animation = 'checkmark 0.3s ease-out';
    productCard.classList.add('product-added');
    
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
            showNotification(`${productName} добавлен в корзину!`);
            updateCartCount(data.cart_count);
            
            setTimeout(() => {
                button.classList.remove('btn-success');
                btnText.style.display = 'inline-block';
                btnIcon.style.display = 'none';
                productCard.classList.remove('product-added');
                currentProductName = '';
            }, 1500);
        } else {
            btnText.textContent = 'Ошибка';
            btnText.style.color = '#dc3545';
            setTimeout(() => {
                btnText.textContent = 'В корзину';
                btnText.style.color = 'white';
                btnText.style.display = 'inline-block';
                btnIcon.style.display = 'none';
                productCard.classList.remove('product-added');
                currentProductName = '';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btnText.textContent = 'Ошибка';
        btnText.style.color = '#dc3545';
        setTimeout(() => {
            btnText.textContent = 'В корзину';
            btnText.style.color = 'white';
            btnText.style.display = 'inline-block';
            btnIcon.style.display = 'none';
            productCard.classList.remove('product-added');
            currentProductName = '';
        }, 2000);
    });
}

function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.style.display = count > 0 ? 'inline' : 'none';
        
        cartCountElement.style.transform = 'scale(1.3)';
        setTimeout(() => {
            cartCountElement.style.transform = 'scale(1)';
        }, 300);
    }
}

function toggleWishlist(productId) {
    const icon = document.getElementById(`wishlist-icon-${productId}`);
    
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&action=toggle'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.action === 'added') {
                icon.textContent = '❤️';
                icon.style.color = '#dc3545';
                showNotification('Добавлено в избранное');
            } else if (data.action === 'removed') {
                icon.textContent = '🤍';
                icon.style.color = '#ccc';
                showNotification('Удалено из избранного');
            }
        } else {
            showNotification(data.message || 'Ошибка', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при обновлении избранного', 'error');
    });
}
function quickView(productId) {
    // В реальном проекте здесь будет AJAX запрос для получения данных о товаре
    alert('Быстрый просмотр товара ID: ' + productId);
    // Можно реализовать модальное окно с подробной информацией
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

// Автоматическое применение фильтров при изменении
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const inputs = filterForm.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            // Для полей цены - проверка минимального и максимального значения
            if (this.name === 'price_min' || this.name === 'price_max') {
                const priceMin = document.querySelector('input[name="price_min"]');
                const priceMax = document.querySelector('input[name="price_max"]');
                
                if (priceMin.value && priceMax.value && parseFloat(priceMin.value) > parseFloat(priceMax.value)) {
                    alert('Минимальная цена не может быть больше максимальной');
                    this.value = '';
                    return;
                }
            }
        });
    });
    
    // Плавная прокрутка к результатам поиска
    if (window.location.search.includes('search=') || 
        window.location.search.includes('category=') ||
        window.location.search.includes('material=')) {
        setTimeout(() => {
            document.querySelector('div[style*="margin-bottom: 30px;"]:last-of-type').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 300);
    }
});
</script>

<?php include 'footer.php'; ?>