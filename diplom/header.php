<?php
require_once 'config.php';

// Получаем цвета оформления
$primary_color = getSetting('primary_color', '#8B4513');
$secondary_color = getSetting('secondary_color', '#A0522D');
$accent_color = getSetting('accent_color', '#D4AF37');
$text_color = getSetting('text_color', '#333333');
$background_color = getSetting('background_color', '#f5f5f5');
$card_color = getSetting('card_color', '#ffffff');
$button_color = getSetting('button_color', '#D4AF37');
$error_color = getSetting('error_color', '#dc3545');
$success_color = getSetting('success_color', '#28a745');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="luxury.ico" type="image/x-icon">
    <title>Luxury Jewelry - Эксклюзивные ювелирные изделия</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: <?php echo getSetting('font_family', "'Helvetica Neue', Arial, sans-serif"); ?>;
            line-height: 1.6;
            background-color: <?php echo $background_color; ?>;
            color: <?php echo $text_color; ?>;
            min-height: 100vh;
            display: inline;
            flex-direction: column;
        }
        
        .header {
            background: linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 25px rgba(139, 69, 19, 0.15);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path fill="white" opacity="0.05" d="M0,0 L100,0 L100,100 Z"/></svg>');
            background-size: cover;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .logo {
            font-size: 28px;
            font-weight: 800;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: translateY(-2px);
        }
        
        .logo-icon {
            font-size: 32px;
            color: <?php echo $accent_color; ?>;
            filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.5));
        }
        
        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        
        .logo-main {
            font-size: 26px;
            font-weight: 300;
            letter-spacing: 1.5px;
        }
        
        .logo-sub {
            font-size: 12px;
            font-weight: 500;
            opacity: 0.9;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-top: 3px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-name {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.95);
            font-size: 15px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 25px;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .nav {
            background-color: rgba(255, 255, 255, 0.98);
            padding: 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e8e8e8;
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .nav-menu {
            list-style: none;
            display: flex;
            gap: 0;
            justify-content: flex-start;
            align-items: center;
            height: 65px;
        }
        
        .nav-menu li {
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        .nav-menu a {
            color: <?php echo $text_color; ?>;
            text-decoration: none;
            padding: 0 25px;
            height: 100%;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            font-weight: 400;
            font-size: 15px;
            position: relative;
            border-bottom: 2px solid transparent;
            letter-spacing: 0.5px;
        }
        
        .nav-menu a:hover {
            background-color: rgba(<?php echo hexdec(substr($primary_color, 1, 2)); ?>, <?php echo hexdec(substr($primary_color, 3, 2)); ?>, <?php echo hexdec(substr($primary_color, 5, 2)); ?>, 0.05);
            color: <?php echo $primary_color; ?>;
        }
        
        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: <?php echo $primary_color; ?>;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        
        .nav-menu a:hover::after {
            width: 100%;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 28px;
            background: linear-gradient(135deg, <?php echo $accent_color; ?> 0%, #FFD700 100%);
            color: #333;
            text-decoration: none;
            border-radius: 25px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #FFD700 0%, <?php echo $accent_color; ?> 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.3);
            color: #333;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #f44336 0%, #e91e63 100%);
            color: white;
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, #e91e63 0%, #f44336 100%);
            color: white;
        }
        
        .admin-badge {
            background: linear-gradient(135deg, <?php echo $accent_color; ?> 0%, #FFD700 100%);
            color: #333;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.5px;
        }
        
        .cart-badge {
            background: linear-gradient(135deg, <?php echo $accent_color; ?> 0%, #FFD700 100%);
            color: #333;
            padding: 4px 10px;
            border-radius: 100%;
            font-size: 12px;
            font-weight: 700;
            min-width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 8px;
        }
        
        .cart-link {
            display: flex;
            align-items: center;
            gap: 10px;
            color: <?php echo $text_color; ?>;
            text-decoration: none;
            padding: 0 25px;
            height: 100%;
            transition: all 0.3s ease;
            font-weight: 400;
            font-size: 15px;
            position: relative;
        }
        
        .cart-link:hover {
            background-color: rgba(<?php echo hexdec(substr($primary_color, 1, 2)); ?>, <?php echo hexdec(substr($primary_color, 3, 2)); ?>, <?php echo hexdec(substr($primary_color, 5, 2)); ?>, 0.05);
            color: <?php echo $primary_color; ?>;
        }
        
        .cart-icon {
            position: relative;
        }
        
        .nav-divider {
            width: 1px;
            height: 30px;
            background: rgba(0,0,0,0.1);
            margin: 0 15px;
        }
        
        /* Бургер-меню */
        .burger-menu-btn {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 24px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            z-index: 1000;
        }
        
        .burger-menu-btn span {
            width: 100%;
            height: 3px;
            background-color: white;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .burger-menu-btn.active span:nth-child(1) {
            transform: translateY(10px) rotate(45deg);
        }
        
        .burger-menu-btn.active span:nth-child(2) {
            opacity: 0;
        }
        
        .burger-menu-btn.active span:nth-child(3) {
            transform: translateY(-10px) rotate(-45deg);
        }
        
        .mobile-nav {
            position: fixed;
            top: 0;
            right: -100%;
            width: 350px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.15);
            z-index: 999;
            transition: right 0.4s ease;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        
        .mobile-nav.active {
            right: 0;
        }
        
        .mobile-nav-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .mobile-nav-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .mobile-nav-header {
            background: linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
            color: white;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mobile-nav-title {
            font-size: 22px;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .mobile-nav-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }
        
        .mobile-nav-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .mobile-user-section {
            padding: 25px;
            background: <?php echo $background_color; ?>;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .mobile-user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .mobile-user-avatar {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, <?php echo $primary_color; ?> 0%, <?php echo $secondary_color; ?> 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: white;
            font-weight: 500;
        }
        
        .mobile-user-details {
            flex: 1;
        }
        
        .mobile-user-name {
            font-weight: 500;
            font-size: 18px;
            color: <?php echo $text_color; ?>;
            margin-bottom: 5px;
        }
        
        .mobile-user-role {
            font-size: 13px;
            color: <?php echo $accent_color; ?>;
            font-weight: 600;
        }
        
        .mobile-buttons {
            display: flex;
            gap: 12px;
        }
        
        .mobile-buttons .btn {
            flex: 1;
            padding: 12px;
            font-size: 14px;
        }
        
        .mobile-nav-menu {
            list-style: none;
            padding: 25px 0;
        }
        
        .mobile-nav-menu li {
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .mobile-nav-menu li:last-child {
            border-bottom: none;
        }
        
        .mobile-nav-menu a {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 18px 25px;
            color: <?php echo $text_color; ?>;
            text-decoration: none;
            font-size: 16px;
            font-weight: 400;
            transition: all 0.3s ease;
        }
        
        .mobile-nav-menu a:hover {
            background: rgba(<?php echo hexdec(substr($primary_color, 1, 2)); ?>, <?php echo hexdec(substr($primary_color, 3, 2)); ?>, <?php echo hexdec(substr($primary_color, 5, 2)); ?>, 0.05);
            color: <?php echo $primary_color; ?>;
        }
        
        .mobile-nav-menu a.active {
            background: rgba(<?php echo hexdec(substr($primary_color, 1, 2)); ?>, <?php echo hexdec(substr($primary_color, 3, 2)); ?>, <?php echo hexdec(substr($primary_color, 5, 2)); ?>, 0.1);
            color: <?php echo $primary_color; ?>;
            border-right: 4px solid <?php echo $primary_color; ?>;
        }
        
        .mobile-nav-icon {
            font-size: 20px;
            width: 24px;
            text-align: center;
            color: <?php echo $accent_color; ?>;
        }
        
        .mobile-cart-section {
            padding: 25px;
            background: <?php echo $background_color; ?>;
            border-top: 1px solid rgba(0,0,0,0.1);
            margin-top: auto;
        }
        
        .mobile-cart-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px;
            background: white;
            border-radius: 12px;
            text-decoration: none;
            color: <?php echo $text_color; ?>;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid #e8e8e8;
        }
        
        .mobile-cart-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            color: <?php echo $primary_color; ?>;
        }
        
        .mobile-cart-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .mobile-cart-count {
            background: linear-gradient(135deg, <?php echo $accent_color; ?> 0%, #FFD700 100%);
            color: #333;
            min-width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
        }
        
        .mobile-admin-section {
            padding: 0 25px 25px;
        }
        
        .mobile-admin-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px;
            background: linear-gradient(135deg, <?php echo $accent_color; ?> 0%, #FFD700 100%);
            color: #333;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
            justify-content: center;
        }
        
        .mobile-admin-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.3);
            background: linear-gradient(135deg, #FFD700 0%, <?php echo $accent_color; ?> 100%);
        }
        
        /* Активная ссылка */
        .nav-menu a.active {
            color: <?php echo $primary_color; ?>;
            background-color: rgba(<?php echo hexdec(substr($primary_color, 1, 2)); ?>, <?php echo hexdec(substr($primary_color, 3, 2)); ?>, <?php echo hexdec(substr($primary_color, 5, 2)); ?>, 0.1);
        }
        
        .nav-menu a.active::after {
            width: 100%;
        }
        
        /* Анимации */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .cart-badge.pulse {
            animation: pulse 0.5s ease-in-out;
        }
        
        /* Ссылка администратора */
        .nav-menu a.admin-link {
            background: linear-gradient(135deg, <?php echo $accent_color; ?> 0%, #FFD700 100%);
            color: #333;
            margin-left: 25px;
            border-radius: 25px;
            padding: 12px 28px;
            height: auto;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        
        .nav-menu a.admin-link:hover {
            background: linear-gradient(135deg, #FFD700 0%, <?php echo $accent_color; ?> 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
        
        .nav-menu a.admin-link::after {
            display: none;
        }
        
        /* Иконки в кнопках */
        .btn-icon {
            font-size: 18px;
        }
        
        /* VIP статус */
        .vip-badge {
            background: linear-gradient(135deg, #8B4513 0%, #D4AF37 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            letter-spacing: 0.5px;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .nav-menu {
                gap: 5px;
            }
            
            .nav-menu a {
                padding: 0 20px;
                font-size: 14px;
            }
            
            .btn {
                padding: 10px 22px;
                font-size: 13px;
            }
        }
        
        @media (max-width: 992px) {
            .nav-menu a {
                padding: 0 15px;
            }
            
            .admin-link {
                margin-left: 15px;
                padding: 10px 20px;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .logo {
                font-size: 22px;
            }
            
            .burger-menu-btn {
                display: flex;
                order: 1;
            }
            
            .logo {
                order: 2;
                margin-left: 15px;
            }
            
            .user-info {
                display: none;
            }
            
            .nav {
                display: none;
            }
            
            .nav-menu {
                flex-direction: column;
                height: auto;
                padding: 20px 0;
            }
            
            .nav-menu li {
                width: 100%;
                height: auto;
            }
            
            .nav-menu a {
                padding: 18px 25px;
                justify-content: flex-start;
                border-bottom: 1px solid rgba(0,0,0,0.05);
            }
            
            .nav-menu a:hover {
                background-color: rgba(<?php echo hexdec(substr($primary_color, 1, 2)); ?>, <?php echo hexdec(substr($primary_color, 3, 2)); ?>, <?php echo hexdec(substr($primary_color, 5, 2)); ?>, 0.1);
            }
            
            .nav-menu a.admin-link {
                margin: 20px 25px 0;
                width: calc(100% - 50px);
            }
            
            .nav-divider {
                display: none;
            }
            
            .mobile-nav {
                width: 320px;
            }
        }
        
        @media (max-width: 480px) {
            .logo-main {
                font-size: 20px;
            }
            
            .logo-sub {
                font-size: 10px;
                letter-spacing: 2px;
            }
            
            .logo-icon {
                font-size: 28px;
            }
            
            .mobile-nav {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Бургер-меню -->
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
    
    <div class="mobile-nav" id="mobileNav">
        <div class="mobile-nav-header">
            <div class="mobile-nav-title">Меню Luxury Jewelry</div>
            <button class="mobile-nav-close" id="mobileNavClose">
                ×
            </button>
        </div>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="mobile-user-section">
            <div class="mobile-user-info">
                <div class="mobile-user-avatar">
                    <?php echo strtoupper(substr(htmlspecialchars($_SESSION['username_luxury']), 0, 1)); ?>
                </div>
                <div class="mobile-user-details">
                    <div class="mobile-user-name"><?php echo htmlspecialchars($_SESSION['username_luxury']); ?></div>
                    <?php if($_SESSION['is_admin_luxury']): ?>
                        <div class="mobile-user-role">Администратор</div>
                    <?php elseif(isUserVIP()): ?>
                        <div class="mobile-user-role">VIP Клиент</div>
                    <?php else: ?>
                        <div class="mobile-user-role">Пользователь</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mobile-buttons">
                <a href="cabinet.php" class="btn">
                    <i class="fas fa-user-circle"></i> Кабинет
                </a>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Выйти
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="mobile-user-section">
            <div class="mobile-user-info">
                <div class="mobile-user-avatar">?</div>
                <div class="mobile-user-details">
                    <div class="mobile-user-name">Гость</div>
                    <div class="mobile-user-role">Не авторизован</div>
                </div>
            </div>
            <div class="mobile-buttons">
                <a href="login.php" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Войти
                </a>
                <a href="register.php" class="btn">
                    <i class="fas fa-user-plus"></i> Регистрация
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <ul class="mobile-nav-menu">
            <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="mobile-nav-icon fas fa-home"></i>
                <span>Главная</span>
            </a></li>
            <li><a href="catalog.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'catalog.php' ? 'active' : ''; ?>">
                <i class="mobile-nav-icon fas fa-gem"></i>
                <span>Каталог</span>
            </a></li>
            <li><a href="about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">
                <i class="mobile-nav-icon fas fa-info-circle"></i>
                <span>О нас</span>
            </a></li>
        </ul>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <div class="mobile-cart-section">
            <?php 
                $cart_count = getCartCount();
            ?>
            <a href="cart.php" class="mobile-cart-link">
                <div class="mobile-cart-content">
                    <i class="fas fa-shopping-cart" style="color: #8B4513;"></i>
                    <span>Корзина</span>
                </div>
                <span class="mobile-cart-count <?php echo $cart_count > 0 ? 'pulse' : ''; ?>">
                    <?php echo $cart_count; ?>
                </span>
            </a>
        </div>
        
        <!-- Избранное -->
        <div class="mobile-cart-section">
            <a href="wishlist.php" class="mobile-cart-link">
                <div class="mobile-cart-content">
                    <i class="fas fa-heart" style="color: #dc3545;"></i>
                    <span>Избранное</span>
                </div>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['is_admin_luxury']) && $_SESSION['is_admin_luxury']): ?>
        <div class="mobile-admin-section">
            <a href="admin.php" class="mobile-admin-link">
                <i class="fas fa-cog"></i>
                <span>Администрирование</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
    
    <header class="header">
        <div class="container">
            <button class="burger-menu-btn" id="burgerMenuBtn">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <a href="index.php" class="logo">
                <div class="logo-icon">💎</div>
                <div class="logo-text">
                    <span class="logo-main">LUXURY JEWELRY</span>
                    <span class="logo-sub">Эксклюзивные изделия</span>
                </div>
            </a>
            
            <div class="user-info">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="user-name">
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($_SESSION['username_luxury']); ?>
                        <?php if($_SESSION['is_admin_luxury']): ?>
                            <span class="admin-badge"><i class="fas fa-crown"></i> Админ</span>
                        <?php elseif(isUserVIP()): ?>
                            <span class="vip-badge"><i class="fas fa-star"></i> VIP</span>
                        <?php endif; ?>
                    </div>
                    <a href="cabinet.php" class="btn">
                        <i class="fas fa-user-circle"></i> Кабинет
                    </a>
                    <a href="logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Выйти
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </a>
                    <a href="register.php" class="btn">
                        <i class="fas fa-user-plus"></i> Регистрация
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    
    <nav class="nav" id="mainNav">
        <div class="nav-container">
            <ul class="nav-menu">
                <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Главная</a></li>
                <li><a href="catalog.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'catalog.php' ? 'active' : ''; ?>">Каталог</a></li>
                <li><a href="about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">О нас</a></li>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                <span class="nav-divider"></span>
                <?php 
                    $cart_count = getCartCount();
                ?>
                <li>
                    <a href="cart.php" class="cart-link <?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i> Корзина
                        <?php if($cart_count > 0): ?>
                            <span id="cart-count" class="cart-badge pulse">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="wishlist.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'wishlist.php' ? 'active' : ''; ?>">
                        <i class="fas fa-heart" style="color: #dc3545;"></i> Избранное
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['is_admin_luxury']) && $_SESSION['is_admin_luxury']): ?>
                    <li><a href="admin.php" class="admin-link"><i class="fas fa-cog"></i> Администрирование</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <main class="main-container" style="max-width: 1400px; margin: 40px auto; padding: 0 20px; min-height: 70vh; flex: 1;">

    <script>
        // Управление бургер-меню
        document.addEventListener('DOMContentLoaded', function() {
            const burgerBtn = document.getElementById('burgerMenuBtn');
            const mobileNav = document.getElementById('mobileNav');
            const mobileNavClose = document.getElementById('mobileNavClose');
            const mobileNavOverlay = document.getElementById('mobileNavOverlay');
            
            // Открытие меню
            burgerBtn.addEventListener('click', function() {
                mobileNav.classList.add('active');
                mobileNavOverlay.classList.add('active');
                burgerBtn.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
            
            // Закрытие меню
            function closeMobileNav() {
                mobileNav.classList.remove('active');
                mobileNavOverlay.classList.remove('active');
                burgerBtn.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            // Закрытие по кнопке
            mobileNavClose.addEventListener('click', closeMobileNav);
            
            // Закрытие по клику на оверлей
            mobileNavOverlay.addEventListener('click', closeMobileNav);
            
            // Закрытие по клику на ссылки в меню
            const mobileLinks = mobileNav.querySelectorAll('a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', closeMobileNav);
            });
            
            // Закрытие по клавише ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileNav.classList.contains('active')) {
                    closeMobileNav();
                }
            });
            
            // Адаптивное поведение
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768 && mobileNav.classList.contains('active')) {
                    closeMobileNav();
                }
            });
            
            // Анимация иконок в навигации
            const navIcons = document.querySelectorAll('.nav-menu a i, .mobile-nav-icon');
            navIcons.forEach(icon => {
                icon.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.2)';
                    this.style.transition = 'transform 0.3s ease';
                });
                icon.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>