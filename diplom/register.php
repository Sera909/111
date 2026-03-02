<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username_luxury']);
    $email = trim($_POST['email_luxury']);
    $password = $_POST['password_luxury'];
    $phone = trim($_POST['phone_luxury']);
    $address = trim($_POST['address_luxury']);
    $birthday = !empty($_POST['birthday_luxury']) ? $_POST['birthday_luxury'] : null;
    
    // Валидация
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email адрес';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        try {
            // Проверка существующего пользователя
            $stmt = $pdo->prepare("SELECT id FROM luxury_users WHERE username_luxury = ? OR email_luxury = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Пользователь с таким именем или email уже существует';
            } else {
                // Хеширование пароля
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Вставка нового пользователя
                $stmt = $pdo->prepare("INSERT INTO luxury_users (username_luxury, email_luxury, password_luxury, phone_luxury, address_luxury, birthday_luxury) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword, $phone, $address, $birthday]);
                
                // Автоматический вход после регистрации
                $user_id = $pdo->lastInsertId();
                $stmt = $pdo->prepare("SELECT * FROM luxury_users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username_luxury'] = $user['username_luxury'];
                $_SESSION['email_luxury'] = $user['email_luxury'];
                $_SESSION['is_admin_luxury'] = $user['is_admin_luxury'];
                $_SESSION['can_edit_design_luxury'] = $user['can_edit_design_luxury'];
                
                $success = 'Регистрация прошла успешно! Добро пожаловать в мир Luxury Jewelry!';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка при регистрации: ' . $e->getMessage();
        }
    }
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 500px; margin: 60px auto; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(139, 69, 19, 0.1); border: 1px solid #e8e8e8;">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 48px; color: #D4AF37; margin-bottom: 10px;">💎</div>
        <h1 style="color: #8B4513; margin-bottom: 10px; font-weight: 300; letter-spacing: 1px;">Регистрация</h1>
        <p style="color: #666; font-size: 14px;">Создайте аккаунт для оформления заказов и получения персональных предложений</p>
    </div>
    
    <?php if($error): ?>
        <div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #f44336;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if($success): ?>
        <div style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #4caf50;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
            <div style="text-align: center; margin-top: 15px;">
                <a href="index.php" style="display: inline-block; padding: 10px 25px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                    На главную
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if(!$success): ?>
    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Имя *</label>
                <input type="text" name="username_luxury" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;"
                       placeholder="Ваше имя">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Email *</label>
                <input type="email" name="email_luxury" required 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;"
                       placeholder="example@mail.ru">
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Пароль *</label>
            <input type="password" name="password_luxury" required 
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;"
                   placeholder="Не менее 6 символов">
            <div style="color: #888; font-size: 12px; margin-top: 5px;">Рекомендуем использовать заглавные и строчные буквы, цифры и спецсимволы</div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Телефон</label>
                <input type="tel" name="phone_luxury" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;"
                       placeholder="+7 (XXX) XXX-XX-XX">
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Дата рождения</label>
                <input type="date" name="birthday_luxury" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9;">
            </div>
        </div>
        
        <div style="margin-bottom: 30px;">
            <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Адрес доставки</label>
            <textarea name="address_luxury" 
                      style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; background: #f9f9f9; height: 100px; resize: vertical;"
                      placeholder="Город, улица, дом, квартира"></textarea>
        </div>
        
        <!-- Преимущества регистрации -->
        <div style="background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); padding: 20px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #e8d9c5;">
            <h4 style="color: #8B4513; margin-bottom: 15px; font-size: 16px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-crown" style="color: #D4AF37;"></i> Преимущества аккаунта
            </h4>
            <ul style="color: #666; font-size: 14px; list-style: none; padding: 0; margin: 0; display: grid; gap: 8px;">
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                    <span>История заказов</span>
                </li>
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                    <span>Специальные предложения</span>
                </li>
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                    <span>Быстрое оформление заказов</span>
                </li>
                <li style="display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                    <span>Личный кабинет</span>
                </li>
            </ul>
        </div>
        
        <!-- Согласие -->
        <div style="margin-bottom: 25px;">
            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                <input type="checkbox" required style="margin-top: 3px; min-width: 16px;">
                <span style="color: #666; font-size: 14px;">
                    Я согласен с <a href="terms.php" style="color: #8B4513; text-decoration: none;">условиями использования</a> и 
                    <a href="privacy.php" style="color: #8B4513; text-decoration: none;">политикой конфиденциальности</a>.
                </span>
            </label>
        </div>
        
        <button type="submit" 
                style="width: 100%; padding: 16px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
            <i class="fas fa-user-plus"></i> Создать аккаунт
        </button>
    </form>
    
    <div style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee;">
        <p style="color: #666; font-size: 15px; margin-bottom: 10px;">
            Уже есть аккаунт?
        </p>
        <a href="login.php" 
           style="display: inline-block; padding: 12px 30px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; text-decoration: none; border-radius: 8px; font-weight: 500; border: 1px solid #e8d9c5; transition: all 0.3s;">
            Войти в аккаунт
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Маска для телефона
    const phoneInput = document.querySelector('input[name="phone_luxury"]');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,1})(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/);
            e.target.value = !x[2] ? x[1] : '+' + x[1] + ' (' + x[2] + (x[3] ? ') ' + x[3] + (x[4] ? '-' + x[4] : '') + (x[5] ? '-' + x[5] : '') : '');
        });
    }
    
    // Валидация формы
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password_luxury"]');
            const email = document.querySelector('input[name="email_luxury"]');
            
            if (password.value.length < 6) {
                e.preventDefault();
                alert('Пароль должен содержать не менее 6 символов');
                password.focus();
                return false;
            }
            
            if (!email.value.includes('@') || !email.value.includes('.')) {
                e.preventDefault();
                alert('Введите корректный email адрес');
                email.focus();
                return false;
            }
            
            return true;
        });
    }
});
</script>

<?php include 'footer.php'; ?>