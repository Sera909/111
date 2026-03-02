<?php include 'header.php'; ?>

<!-- Основной контент -->
<div class="main-content">
    <!-- Hero Section -->
    <section class="hero-section" style="background: linear-gradient(135deg, rgba(139, 69, 19, 0.9) 0%, rgba(160, 82, 45, 0.9) 100%), url('https://images.unsplash.com/photo-1533134486753-c833f0ed4866?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80'); background-size: cover; background-position: center; color: white; padding: 100px 20px; text-align: center; border-radius: 0 0 30px 30px; margin-bottom: 60px;">
        <div class="hero-content" style="max-width: 800px; margin: 0 auto;">
            <h1 class="hero-title" style="font-size: 3.5rem; font-weight: 300; margin-bottom: 25px; letter-spacing: 2px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                Исключительная роскошь в каждой детали
            </h1>
            <p class="hero-subtitle" style="font-size: 1.3rem; opacity: 0.95; margin-bottom: 40px; line-height: 1.6;">
                Эксклюзивные ювелирные изделия ручной работы. Создавайте свою историю с Luxury Jewelry
            </p>
            <div class="hero-buttons" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="catalog.php" class="hero-button" style="display: inline-block; background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%); color: #333; padding: 18px 45px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s; box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);">
                    <i class="fas fa-gem"></i> Смотреть коллекцию
                </a>
                <a href="collections.php" class="hero-button secondary" style="display: inline-block; background: transparent; color: white; padding: 18px 45px; border-radius: 30px; text-decoration: none; font-weight: 600; font-size: 1.1rem; transition: all 0.3s; border: 2px solid rgba(255, 255, 255, 0.8);">
                    <i class="fas fa-crown"></i> Коллекции
                </a>
            </div>
        </div>
    </section>

    <!-- Преимущества -->
    <section class="features-section" style="max-width: 1400px; margin: 0 auto 80px; padding: 0 20px;">
        <h2 class="section-title" style="text-align: center; font-size: 2.5rem; color: #8B4513; margin-bottom: 50px; font-weight: 300; letter-spacing: 1px;">
            Почему выбирают Luxury Jewelry
        </h2>
        <div class="features-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <div class="feature" style="text-align: center; padding: 40px 30px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s; border: 1px solid #f0f0f0;">
                <div class="feature-icon" style="font-size: 4rem; margin-bottom: 25px; color: #D4AF37;">💎</div>
                <h4 style="color: #333; margin-bottom: 15px; font-size: 1.4rem; font-weight: 500;">Эксклюзивные изделия</h4>
                <p style="color: #666; line-height: 1.6; font-size: 1rem;">Уникальные дизайны, созданные лучшими ювелирами</p>
            </div>
            <div class="feature" style="text-align: center; padding: 40px 30px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s; border: 1px solid #f0f0f0;">
                <div class="feature-icon" style="font-size: 4rem; margin-bottom: 25px; color: #D4AF37;">⭐</div>
                <h4 style="color: #333; margin-bottom: 15px; font-size: 1.4rem; font-weight: 500;">Сертификаты качества</h4>
                <p style="color: #666; line-height: 1.6; font-size: 1rem;">Все изделия имеют сертификаты подлинности</p>
            </div>
            <div class="feature" style="text-align: center; padding: 40px 30px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s; border: 1px solid #f0f0f0;">
                <div class="feature-icon" style="font-size: 4rem; margin-bottom: 25px; color: #D4AF37;">🎁</div>
                <h4 style="color: #333; margin-bottom: 15px; font-size: 1.4rem; font-weight: 500;">Подарочная упаковка</h4>
                <p style="color: #666; line-height: 1.6; font-size: 1rem;">Бесплатная подарочная упаковка премиум-класса</p>
            </div>
            <div class="feature" style="text-align: center; padding: 40px 30px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s; border: 1px solid #f0f0f0;">
                <div class="feature-icon" style="font-size: 4rem; margin-bottom: 25px; color: #D4AF37;">💍</div>
                <h4 style="color: #333; margin-bottom: 15px; font-size: 1.4rem; font-weight: 500;">Индивидуальный подход</h4>
                <p style="color: #666; line-height: 1.6; font-size: 1rem;">Консультации и подбор изделий под ваш стиль</p>
            </div>
        </div>
    </section>

    <!-- Популярные категории -->
    <section class="categories-section" style="max-width: 1400px; margin: 0 auto 100px; padding: 0 20px;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h2 class="section-title" style="font-size: 2.5rem; color: #8B4513; font-weight: 300; letter-spacing: 1px;">
                Популярные категории
            </h2>
            <a href="catalog.php" class="view-all" style="color: #8B4513; text-decoration: none; font-weight: 500; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                Все категории <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="categories-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px;">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM luxury_categories ORDER BY created_at_luxury DESC LIMIT 4");
                $categories = $stmt->fetchAll();
                
                foreach ($categories as $index => $category): 
                    $colors = ['#8B4513', '#A0522D', '#D4AF37', '#8B7355'];
                    $icons = ['💍', '👑', '💎', '⌚'];
            ?>
            <a href="catalog.php?category=<?php echo $category['id']; ?>" class="category-card" style="display: block; background: linear-gradient(135deg, <?php echo $colors[$index % 4]; ?> 0%, <?php echo $colors[($index + 1) % 4]; ?> 100%); color: white; border-radius: 20px; padding: 40px 30px; text-decoration: none; transition: transform 0.3s, box-shadow 0.3s; position: relative; overflow: hidden; min-height: 250px;">
                <div style="position: absolute; top: 20px; right: 20px; font-size: 3rem; opacity: 0.2;"><?php echo $icons[$index % 4]; ?></div>
                <h3 style="font-size: 1.8rem; font-weight: 400; margin-bottom: 15px; position: relative; z-index: 1;"><?php echo htmlspecialchars($category['name_luxury']); ?></h3>
                <?php if(!empty($category['description_luxury'])): ?>
                    <p style="opacity: 0.9; line-height: 1.5; position: relative; z-index: 1;"><?php echo htmlspecialchars($category['description_luxury']); ?></p>
                <?php endif; ?>
                <div style="position: absolute; bottom: 30px; left: 30px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; opacity: 0.8; z-index: 1;">
                    <span>Смотреть коллекцию</span>
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            <?php endforeach; 
            } catch (PDOException $e) {
                // Категории по умолчанию
                $default_categories = [
                    ['Кольца', '💍', '#8B4513'],
                    ['Серьги', '👑', '#A0522D'],
                    ['Колье', '💎', '#D4AF37'],
                    ['Браслеты', '⌚', '#8B7355']
                ];
                
                foreach ($default_categories as $index => $category): ?>
                <a href="catalog.php?category=<?php echo $index + 1; ?>" class="category-card" style="display: block; background: linear-gradient(135deg, <?php echo $category[2]; ?> 0%, <?php echo $colors[($index + 1) % 4] ?? '#8B4513'; ?> 100%); color: white; border-radius: 20px; padding: 40px 30px; text-decoration: none; transition: transform 0.3s, box-shadow 0.3s; position: relative; overflow: hidden; min-height: 250px;">
                    <div style="position: absolute; top: 20px; right: 20px; font-size: 3rem; opacity: 0.2;"><?php echo $category[1]; ?></div>
                    <h3 style="font-size: 1.8rem; font-weight: 400; margin-bottom: 15px; position: relative; z-index: 1;"><?php echo $category[0]; ?></h3>
                    <div style="position: absolute; bottom: 30px; left: 30px; display: flex; align-items: center; gap: 10px; font-size: 0.9rem; opacity: 0.8; z-index: 1;">
                        <span>Смотреть коллекцию</span>
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>
                <?php endforeach;
            }
            ?>
        </div>
    </section>

    <!-- Новинки -->
    <section class="new-products" style="max-width: 1400px; margin: 0 auto 100px; padding: 0 20px;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h2 class="section-title" style="font-size: 2.5rem; color: #8B4513; font-weight: 300; letter-spacing: 1px;">
                Новые поступления
            </h2>
            <a href="catalog.php?new=1" class="view-all" style="color: #8B4513; text-decoration: none; font-weight: 500; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                Все новинки <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            <?php
            try {
                $sql = "SELECT * FROM luxury_jewelry WHERE stock_luxury > 0 ORDER BY created_at_luxury DESC LIMIT 6";
                $stmt = $pdo->query($sql);
                $new_products = $stmt->fetchAll();
                
                foreach ($new_products as $product): 
            ?>
            <div class="product-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: transform 0.3s; border: 1px solid #f0f0f0;">
                <div style="height: 250px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                    <?php if(!empty($product['image_luxury'])): ?>
                        <img src="<?php echo htmlspecialchars($product['image_luxury']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name_luxury']); ?>"
                             style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    <?php else: ?>
                        <span style="color: #d4b89c; font-size: 4rem; opacity: 0.5;">💎</span>
                    <?php endif; ?>
                    <div style="position: absolute; top: 15px; left: 15px; background: linear-gradient(135deg, #D4AF37, #FFD700); color: white; padding: 6px 15px; border-radius: 15px; font-size: 12px; font-weight: 600;">
                        НОВИНКА
                    </div>
                </div>
                <div style="padding: 25px;">
                    <h4 style="margin: 0 0 10px 0; color: #333; font-size: 1.1rem; font-weight: 400; height: 50px; overflow: hidden;">
                        <a href="product.php?id=<?php echo $product['id']; ?>" style="color: inherit; text-decoration: none;">
                            <?php echo htmlspecialchars($product['name_luxury']); ?>
                        </a>
                    </h4>
                    <div style="color: #D4AF37; font-weight: 600; font-size: 1.4rem; margin-bottom: 15px;">
                        <?php echo number_format($product['price_luxury'], 0, ',', ' '); ?> ₽
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo htmlspecialchars($product['name_luxury']); ?>')" 
                                style="flex: 1; padding: 12px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.3s;">
                            В корзину
                        </button>
                        <a href="product.php?id=<?php echo $product['id']; ?>" 
                           style="padding: 12px 20px; background-color: #f8f9fa; color: #666; text-decoration: none; border-radius: 8px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; transition: all 0.3s; font-size: 14px;">
                            Подробнее
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; 
            } catch (PDOException $e) {
                echo '<div style="text-align: center; padding: 40px; color: #666;">Новинки скоро появятся!</div>';
            }
            ?>
        </div>
    </section>

    <!-- О нас -->
    <section class="about-section" style="max-width: 1400px; margin: 0 auto 80px; padding: 60px 20px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 30px; text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2 style="color: #8B4513; font-size: 2.8rem; font-weight: 300; margin-bottom: 30px; letter-spacing: 1px;">
                Luxury Jewelry
            </h2>
            <p style="color: #666; font-size: 1.2rem; line-height: 1.8; margin-bottom: 40px;">
                Мы создаём не просто украшения, а произведения искусства, которые передаются из поколения в поколение. 
                Каждое изделие — это сочетание многовековых традиций ювелирного мастерства и современных технологий.
            </p>
            <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: #8B4513; margin-bottom: 10px;">10+</div>
                    <div style="color: #666; font-size: 1rem;">Лет на рынке</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: #8B4513; margin-bottom: 10px;">5000+</div>
                    <div style="color: #666; font-size: 1rem;">Довольных клиентов</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: #8B4513; margin-bottom: 10px;">100%</div>
                    <div style="color: #666; font-size: 1rem;">Качество и гарантия</div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function addToCart(productId, productName) {
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
        } else {
            showNotification(data.message || 'Ошибка при добавлении в корзину', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Ошибка при добавлении в корзину', 'error');
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const colors = {
        'success': 'linear-gradient(135deg, #28a745, #20c997)',
        'error': 'linear-gradient(135deg, #dc3545, #e83e8c)'
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

// Анимация карточек при наведении
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card, .category-card, .feature');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.1)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
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
</script>

<?php include 'footer.php'; ?>