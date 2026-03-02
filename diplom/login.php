<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email_luxury']);
    $password = $_POST['password_luxury'];
    
    if (empty($email) || empty($password)) {
        $error = 'Пожалуйста, заполните все поля';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM luxury_users WHERE email_luxury = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_luxury'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username_luxury'] = $user['username_luxury'];
                $_SESSION['email_luxury'] = $user['email_luxury'];
                $_SESSION['is_admin_luxury'] = $user['is_admin_luxury'];
                $_SESSION['can_edit_design_luxury'] = $user['can_edit_design_luxury'];
                
                // Обновляем время последнего входа (если есть поле)
                try {
                    $updateStmt = $pdo->prepare("UPDATE luxury_users SET last_login_luxury = NOW() WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                } catch (Exception $e) {
                    // Поле может не существовать, это нормально
                }
                
                header('Location: index.php');
                exit();
            } else {
                $error = 'Неверный email или пароль';
            }
        } catch (PDOException $e) {
            $error = 'Ошибка при авторизации: ' . $e->getMessage();
        }
    }
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 450px; margin: 80px auto; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(139, 69, 19, 0.1); border: 1px solid #e8e8e8;">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 48px; color: #D4AF37; margin-bottom: 10px;">🔐</div>
        <h1 style="color: #8B4513; margin-bottom: 10px; font-weight: 300; letter-spacing: 1px;">Вход в Luxury Jewelry</h1>
        <p style="color: #666; font-size: 14px;">Войдите в свой аккаунт для доступа к персональным предложениям</p>
    </div>
    
    <?php if($error): ?>
        <div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid #f44336;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; color: #555; font-size: 14px; font-weight: 500;">Email *</label>
            <input type="email" name="email_luxury" required 
                   style="width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; transition: all 0.3s;"
                   placeholder="example@luxury-jewelry.ru"
                   onfocus="this.style.borderColor='#8B4513'; this.style.boxShadow='0 0 0 2px rgba(139, 69, 19, 0.1)'"
                   onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none'">
        </div>
        
        <div style="margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <label style="color: #555; font-size: 14px; font-weight: 500;">Пароль *</label>
                <a href="forgot_password.php" style="color: #8B4513; font-size: 13px; text-decoration: none;">Забыли пароль?</a>
            </div>
            <input type="password" name="password_luxury" required 
                   style="width: 100%; padding: 14px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; transition: all 0.3s;"
                   placeholder="Введите пароль"
                   onfocus="this.style.borderColor='#8B4513'; this.style.boxShadow='0 0 0 2px rgba(139, 69, 19, 0.1)'"
                   onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none'">
        </div>
        
        <div style="margin-bottom: 30px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" style="width: 18px; height: 18px;">
                <span style="color: #666; font-size: 14px;">Запомнить меня</span>
            </label>
        </div>
        
        <button type="submit" 
                style="width: 100%; padding: 16px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
            <i class="fas fa-sign-in-alt"></i> Войти в аккаунт
        </button>
    </form>
    
    <!-- Разделитель -->
    <div style="position: relative; text-align: center; margin: 30px 0;">
        <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: #eee;"></div>
        <span style="position: relative; background: white; padding: 0 15px; color: #888; font-size: 14px;">или</span>
    </div>
    
    <!-- Быстрый вход (для демо) -->
    <div style="margin-bottom: 30px;">
        <div style="color: #555; font-size: 14px; margin-bottom: 15px; text-align: center;">Быстрый вход для тестирования</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <button onclick="fillDemoCredentials('admin@luxury-jewelry.ru', 'AdminLuxury123!')" 
                    style="padding: 12px; background: linear-gradient(135deg, #D4AF37 0%, #FFD700 100%); color: #333; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; transition: all 0.3s;">
                <i class="fas fa-crown"></i> Администратор
            </button>
            <button onclick="fillDemoCredentials('vip@luxury-jewelry.ru', 'VipClient123!')" 
                    style="padding: 12px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; font-size: 14px; cursor: pointer; transition: all 0.3s;">
                <i class="fas fa-star"></i> VIP Клиент
            </button>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee;">
        <p style="color: #666; font-size: 15px; margin-bottom: 15px;">
            Ещё нет аккаунта?
        </p>
        <a href="register.php" 
           style="display: inline-block; padding: 14px 40px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); color: #8B4513; text-decoration: none; border-radius: 8px; font-weight: 500; border: 1px solid #e8d9c5; transition: all 0.3s;">
            <i class="fas fa-user-plus"></i> Создать аккаунт
        </a>
    </div>
    
    <!-- Преимущества входа -->
    <div style="margin-top: 40px; padding: 25px; background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 12px; border: 1px solid #e8d9c5;">
        <h4 style="color: #8B4513; margin-bottom: 15px; font-size: 16px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-crown" style="color: #D4AF37;"></i> Преимущества входа
        </h4>
        <ul style="color: #666; font-size: 14px; list-style: none; padding: 0; margin: 0; display: grid; gap: 10px;">
            <li style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                <span>История заказов</span>
            </li>
            <li style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                <span>Избранные товары</span>
            </li>
            <li style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                <span>Специальные предложения</span>
            </li>
            <li style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check" style="color: #4caf50; font-size: 12px;"></i>
                <span>Быстрое оформление заказов</span>
            </li>
        </ul>
    </div>
</div>

<script>
function fillDemoCredentials(email, password) {
    document.querySelector('input[name="email_luxury"]').value = email;
    document.querySelector('input[name="password_luxury"]').value = password;
    
    // Показать сообщение
    const message = document.createElement('div');
    message.innerHTML = `
        <div style="position: fixed; top: 20px; right: 20px; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 10000; animation: slideIn 0.3s ease-out;">
            <i class="fas fa-check-circle"></i> Данные для входа заполнены
        </div>
    `;
    document.body.appendChild(message);
    
    setTimeout(() => {
        message.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => message.remove(), 300);
    }, 2000);
    
    // Автофокус на кнопку входа
    setTimeout(() => {
        document.querySelector('button[type="submit"]').focus();
    }, 100);
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
    
    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(139, 69, 19, 0.15);
    }
`;
document.head.appendChild(style);

// Валидация формы
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const emailInput = document.querySelector('input[name="email_luxury"]');
    const passwordInput = document.querySelector('input[name="password_luxury"]');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Проверка email
        if (!emailInput.value || !emailInput.value.includes('@')) {
            emailInput.style.borderColor = '#f44336';
            emailInput.style.boxShadow = '0 0 0 2px rgba(244, 67, 54, 0.1)';
            isValid = false;
        }
        
        // Проверка пароля
        if (!passwordInput.value) {
            passwordInput.style.borderColor = '#f44336';
            passwordInput.style.boxShadow = '0 0 0 2px rgba(244, 67, 54, 0.1)';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Показать сообщение об ошибке
            if (!document.querySelector('.validation-error')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'validation-error';
                errorDiv.innerHTML = `
                    <div style="background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); color: #c62828; padding: 15px; border-radius: 8px; margin-top: 20px; border-left: 4px solid #f44336;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>Пожалуйста, заполните все поля корректно</span>
                        </div>
                    </div>
                `;
                form.appendChild(errorDiv);
            }
            
            return false;
        }
    });
    
    // Сброс ошибок при вводе
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '#ddd';
            this.style.boxShadow = 'none';
            const errorDiv = document.querySelector('.validation-error');
            if (errorDiv) errorDiv.remove();
        });
    });
});
</script>

<?php include 'footer.php'; ?>