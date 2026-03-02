</main>
    
<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-column">
                    <div class="footer-logo">
                        <i class="fas fa-gem footer-logo-icon"></i>
                        <div class="footer-logo-text">
                            <span class="footer-logo-main">Luxury Jewelry</span>
                            <span class="footer-logo-sub">Эксклюзивные ювелирные изделия</span>
                        </div>
                    </div>
                    <p class="footer-description">
                        Мы создаём украшения, которые станут частью вашей истории. Каждое изделие — это воплощение мастерства, 
                        роскоши и индивидуальности.
                    </p>
<div class="footer-social">
    <a href="<?php echo getSetting('telegram_url', '#'); ?>" class="social-link" aria-label="Telegram" title="Telegram">
        <i class="fab fa-telegram-plane"></i>
    </a>
    <a href="<?php echo getSetting('vk_url', '#'); ?>" class="social-link" aria-label="VK" title="ВКонтакте">
        <i class="fab fa-vk"></i>
    </a>
    <a href="<?php echo getSetting('rutube_url', '#'); ?>" class="social-link" aria-label="Rutube" title="Rutube">
        <svg class="custom-icon" viewBox="0 0 24 24" width="24" height="24" style="fill: currentColor;">
            <path d="M20.8,8.6c-0.2-1.5-0.4-2.6-0.6-3.2c-0.2-0.7-0.5-1.3-0.7-1.6c-0.3-0.4-0.7-0.7-1.1-0.9c-0.5-0.2-1.3-0.4-2.4-0.5
                c-1.1-0.1-2.6-0.2-4.4-0.2H8.4c-1.8,0-3.3,0.1-4.4,0.2C2.9,3.1,2.1,3.3,1.6,3.5C1.2,3.7,0.9,4,0.6,4.4C0.3,4.7,0.1,5.3,0,6
                C0,6.7-0.1,7.7-0.1,9v6c0,1.3,0,2.3,0.1,3c0.1,0.7,0.3,1.3,0.6,1.6c0.3,0.4,0.7,0.7,1.1,0.9c0.5,0.2,1.3,0.4,2.4,0.5
                c1.1,0.1,2.6,0.2,4.4,0.2h3.3c1.8,0,3.3-0.1,4.4-0.2c1.1-0.1,1.9-0.3,2.4-0.5c0.5-0.2,0.8-0.5,1.1-0.9c0.3-0.4,0.5-0.9,0.6-1.6
                c0.1-0.7,0.2-1.7,0.2-3V9C21,8.1,20.9,8.6,20.8,8.6z M8.8,15.6V8.4l5.6,3.6L8.8,15.6z"/>
        </svg>
    </a>

</div>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">Коллекции</h3>
                    <ul class="footer-links">
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM luxury_categories ORDER BY name_luxury LIMIT 5");
                            $categories = $stmt->fetchAll();
                            
                            foreach ($categories as $category): ?>
                                <li>
                                    <a href="catalog.php?category=<?php echo $category['id']; ?>">
                                        <i class="fas fa-sparkle" style="font-size: 12px;"></i>
                                        <?php echo htmlspecialchars($category['name_luxury']); ?>
                                    </a>
                                </li>
                            <?php endforeach;
                        } catch (PDOException $e) {
                            // Категории по умолчанию
                            $default_categories = [
                                ['Кольца', '?category=1'],
                                ['Серьги', '?category=2'],
                                ['Колье', '?category=3'],
                                ['Браслеты', '?category=4'],
                                ['Часы', '?category=5']
                            ];
                            
                            foreach ($default_categories as $category): ?>
                                <li>
                                    <a href="catalog.php<?php echo $category[1]; ?>">
                                        <i class="fas fa-sparkle" style="font-size: 12px;"></i>
                                        <?php echo htmlspecialchars($category[0]); ?>
                                    </a>
                                </li>
                            <?php endforeach;
                        }
                        ?>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3 class="footer-title">Клиентам</h3>
                    <ul class="footer-links">
                        <li>
                            <a href="about.php">
                                <i class="fas fa-star"></i>
                                О компании
                            </a>
                        </li>
                        <li>
                            <a href="delivery.php">
                                <i class="fas fa-truck"></i>
                                Доставка и оплата
                            </a>
                        </li>
                        <li>
                            <a href="guarantee.php">
                                <i class="fas fa-shield-alt"></i>
                                Гарантия и сертификаты
                            </a>
                        </li>
                        <li>
                            <a href="contacts.php">
                                <i class="fas fa-map-marker-alt"></i>
                                Контакты
                            </a>
                        </li>
                        <li>
                            <a href="faq.php">
                                <i class="fas fa-question-circle"></i>
                                FAQ
                            </a>
                        </li>
                    </ul>
                </div>
            
                <div class="footer-column">
                    <h3 class="footer-title">Контакты</h3>
                    <div class="footer-contacts">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <div class="contact-label">Email</div>
                                <a href="mailto:<?php echo getSetting('shop_email', 'info@luxury-jewelry.ru'); ?>" class="contact-value">
                                    <?php echo getSetting('shop_email', 'info@luxury-jewelry.ru'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <div class="contact-label">Телефон</div>
                                <a href="tel:<?php echo str_replace([' ', '(', ')', '-'], '', getSetting('shop_phone', '+7 (999) 123-45-67')); ?>" class="contact-value">
                                    <?php echo getSetting('shop_phone', '+7 (999) 123-45-67'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div>
                                <div class="contact-label">Адрес</div>
                                <div class="contact-value"><?php echo getSetting('shop_address', 'Москва, ул. Тверская, 10'); ?></div>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="contact-label">Часы работы</div>
                                <div class="contact-value">Пн-Пт: 10:00 - 20:00<br>Сб-Вс: 11:00 - 19:00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="footer-container">
            <div class="footer-bottom-content">
                <div class="footer-copyright">
                    <span>© <?php echo date('Y'); ?> Luxury Jewelry</span>
                    <span class="footer-divider">•</span>
                    <span>Все права защищены</span>
                    <span class="footer-divider">•</span>
                    <a href="privacy.php" style="color: rgba(255, 255, 255, 0.8); text-decoration: none;">Политика конфиденциальности</a>
                </div>
                <div class="footer-payments">
                    <div class="payment-methods">
                        <i class="fab fa-cc-visa payment-icon"></i>
                        <i class="fab fa-cc-mastercard payment-icon"></i>
                        <i class="fab fa-cc-mir payment-icon"></i>
                        <i class="fab fa-cc-amex payment-icon"></i>
                        <i class="fas fa-lock payment-icon" title="SSL защита"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
    // Анимация для футера при прокрутке
    window.addEventListener('scroll', function() {
        const footer = document.querySelector('.site-footer');
        const scrollPosition = window.scrollY;
        const windowHeight = window.innerHeight;
        const documentHeight = document.body.clientHeight;
        
        if (scrollPosition + windowHeight >= documentHeight - 50) {
            footer.classList.add('visible');
        }
    });

    // Мобильное меню
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
    }

    // Обновление количества в корзине
    function updateCartCount(count) {
        const cartBadge = document.getElementById('cart-count');
        if (cartBadge) {
            cartBadge.textContent = count;
            cartBadge.classList.add('pulse');
            setTimeout(() => {
                cartBadge.classList.remove('pulse');
            }, 500);
        }
    }
    
    // Анимация социальных иконок
    document.addEventListener('DOMContentLoaded', function() {
        const socialLinks = document.querySelectorAll('.social-link');
        socialLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.1)';
            });
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    });
</script>
</body>
</html>

<style>
/* Footer Styles */
.site-footer {
    background: linear-gradient(135deg, <?php echo getSetting('primary_color', '#8B4513'); ?> 0%, <?php echo getSetting('secondary_color', '#A0522D'); ?> 100%);
    color: white;
    margin-top: 100px;
    position: relative;
    overflow: hidden;
}

.site-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="white" opacity="0.03" d="M0,100 L50,0 L100,100 Z"/></svg>');
    background-size: cover;
}

.footer-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 1;
}

.footer-top {
    padding: 70px 0 50px;
}

.footer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 50px;
}

.footer-column {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 18px;
    margin-bottom: 15px;
}

.footer-logo-icon {
    font-size: 36px;
    opacity: 0.9;
    color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;
    filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.3));
}

.footer-logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.footer-logo-main {
    font-size: 26px;
    font-weight: 700;
    color: white;
    letter-spacing: 0.5px;
}

.footer-logo-sub {
    font-size: 14px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.85);
    letter-spacing: 1px;
    text-transform: uppercase;
}

.footer-description {
    color: rgba(255, 255, 255, 0.85);
    line-height: 1.7;
    font-size: 15px;
    max-width: 300px;
}

.footer-social {
    display: flex;
    gap: 18px;
    margin-top: 10px;
}

.social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 46px;
    height: 46px;
    background: rgba(255, 255, 255, 0.12);
    border-radius: 50%;
    color: white;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    font-size: 20px;
    position: relative;
    overflow: hidden;
}

.social-link::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.3), rgba(255, 215, 0, 0.3));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.social-link:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-3px) scale(1.1);
    color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;
}

.social-link:hover::after {
    opacity: 1;
}

.footer-title {
    font-size: 22px;
    font-weight: 600;
    color: white;
    margin-bottom: 20px;
    position: relative;
    padding-bottom: 12px;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 2px;
    background: <?php echo getSetting('accent_color', '#D4AF37'); ?>;
}

.footer-links {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.footer-links li a {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    padding: 5px 0;
    font-size: 15px;
}

.footer-links li a i {
    width: 18px;
    text-align: center;
    font-size: 14px;
    color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;
    transition: transform 0.3s ease;
}

.footer-links li a:hover {
    color: white;
    transform: translateX(5px);
}

.footer-links li a:hover i {
    color: white;
    transform: rotate(20deg);
}

.footer-contacts {
    display: flex;
    flex-direction: column;
    gap: 22px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 18px;
}

.contact-icon {
    font-size: 18px;
    opacity: 0.9;
    min-width: 24px;
    color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;
    text-align: center;
    margin-top: 3px;
}

.contact-label {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.contact-value {
    color: rgba(255, 255, 255, 0.95);
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: color 0.3s ease;
    line-height: 1.5;
}

.contact-value:hover {
    color: <?php echo getSetting('accent_color', '#D4AF37'); ?>;
}

.footer-bottom {
    background: rgba(0, 0, 0, 0.15);
    padding: 28px 0;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 25px;
}

.footer-copyright {
    display: flex;
    align-items: center;
    gap: 15px;
    color: rgba(255, 255, 255, 0.8);
    font-size: 15px;
    flex-wrap: wrap;
}

.footer-divider {
    opacity: 0.5;
}

.footer-payments {
    display: flex;
    align-items: center;
    gap: 18px;
}

.payment-methods {
    display: flex;
    gap: 18px;
    align-items: center;
}

.payment-icon {
    font-size: 30px;
    opacity: 0.9;
    transition: all 0.3s ease;
    color: rgba(255, 255, 255, 0.9);
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.payment-icon:hover {
    opacity: 1;
    color: white;
    transform: translateY(-2px);
}

/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.footer-column {
    animation: fadeInUp 0.5s ease forwards;
}

.footer-column:nth-child(1) { animation-delay: 0.1s; }
.footer-column:nth-child(2) { animation-delay: 0.2s; }
.footer-column:nth-child(3) { animation-delay: 0.3s; }
.footer-column:nth-child(4) { animation-delay: 0.4s; }

/* Responsive */
@media (max-width: 1024px) {
    .footer-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
    }
    
    .footer-top {
        padding: 50px 0 40px;
    }
}

@media (max-width: 768px) {
    .footer-top {
        padding: 40px 0 30px;
    }
    
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 35px;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .footer-copyright {
        flex-direction: column;
        gap: 10px;
    }
    
    .footer-divider {
        display: none;
    }
    
    .footer-logo {
        justify-content: center;
        text-align: center;
        flex-direction: column;
    }
    
    .footer-title {
        text-align: center;
    }
    
    .footer-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .footer-description {
        max-width: 100%;
        text-align: center;
    }
    
    .footer-social {
        justify-content: center;
    }
    
    .footer-links li a {
        justify-content: center;
    }
    
    .contact-item {
        justify-content: center;
        text-align: center;
        flex-direction: column;
        align-items: center;
    }
    
    .contact-icon {
        margin-bottom: 8px;
    }
    
    .payment-methods {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .footer-container {
        padding: 0 15px;
    }
    
    .footer-top {
        padding: 30px 0 25px;
    }
    
    .payment-icon {
        font-size: 26px;
    }
}
</style>