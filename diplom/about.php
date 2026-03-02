<?php
require_once 'config.php';

// Получаем информацию о компании из настроек
try {
    $shop_name = getSetting('shop_name', 'Luxury Jewelry');
    $shop_email = getSetting('shop_email', 'info@luxury-jewelry.ru');
    $shop_phone = getSetting('shop_phone', '+7 (999) 123-45-67');
    $shop_address = getSetting('shop_address', 'Москва, ул. Тверская, 10');
} catch (Exception $e) {
    // Используем значения по умолчанию
}
?>

<?php include 'header.php'; ?>

<div style="max-width: 1400px; margin: 0 auto; padding: 40px 20px;">
    <!-- Заголовок -->
    <div style="text-align: center; margin-bottom: 60px;">
        <h1 style="color: #8B4513; font-size: 3.5rem; font-weight: 300; letter-spacing: 2px; margin-bottom: 20px; line-height: 1.2;">
            О компании <span style="color: #D4AF37;">Luxury Jewelry</span>
        </h1>
        <p style="color: #666; font-size: 1.2rem; max-width: 800px; margin: 0 auto; line-height: 1.6;">
            Мы создаём историю. Каждое наше изделие — это сочетание многовековых традиций, 
            современного дизайна и бескомпромиссного качества.
        </p>
    </div>
    
    <!-- История компании -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; margin-bottom: 80px;">
        <div>
            <h2 style="color: #8B4513; font-size: 2.5rem; font-weight: 300; margin-bottom: 30px; letter-spacing: 1px;">
                Наша история
            </h2>
            <div style="color: #333; font-size: 1.1rem; line-height: 1.8; margin-bottom: 25px;">
                <p>Основанная в 2010 году, компания Luxury Jewelry начала свой путь с небольшой мастерской, 
                где ювелиры-энтузиасты создавали уникальные изделия для ценителей прекрасного.</p>
                
                <p>С годами наша страсть к совершенству и внимание к деталям позволили нам вырасти в одну 
                из ведущих ювелирных компаний России, чьи изделия ценятся во всём мире.</p>
                
                <p>Сегодня мы объединяем традиции старых мастеров с инновационными технологиями, 
                создавая украшения, которые становятся семейными реликвиями.</p>
            </div>
            
            <div style="display: flex; gap: 30px; margin-top: 40px;">
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: #D4AF37; font-weight: 300; line-height: 1;">10+</div>
                    <div style="color: #666; font-size: 0.9rem; margin-top: 5px;">Лет на рынке</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: #D4AF37; font-weight: 300; line-height: 1;">50+</div>
                    <div style="color: #666; font-size: 0.9rem; margin-top: 5px;">Мастеров-ювелиров</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 3rem; color: #D4AF37; font-weight: 300; line-height: 1;">5000+</div>
                    <div style="color: #666; font-size: 0.9rem; margin-top: 5px;">Уникальных изделий</div>
                </div>
            </div>
        </div>
        
        <div>
            <div style="background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 20px; padding: 40px; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                <div style="font-size: 8rem; color: #d4b89c; margin-bottom: 20px; opacity: 0.5;">💎</div>
                <h3 style="color: #8B4513; font-size: 1.8rem; font-weight: 300; margin-bottom: 20px;">
                    Философия бренда
                </h3>
                <p style="color: #666; line-height: 1.6; font-style: italic;">
                    "Мы верим, что настоящее украшение — это не просто аксессуар, 
                    а продолжение личности, отражение внутреннего мира и истории своего владельца."
                </p>
                <div style="margin-top: 30px; color: #A0522D; font-weight: 500;">
                    — Основатель Luxury Jewelry
                </div>
            </div>
        </div>
    </div>
    
    <!-- Наши ценности -->
    <div style="margin-bottom: 80px;">
        <h2 style="color: #8B4513; font-size: 2.5rem; font-weight: 300; text-align: center; margin-bottom: 50px; letter-spacing: 1px;">
            Наши ценности
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <div style="background: white; padding: 40px 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; text-align: center; transition: transform 0.3s;">
                <div style="font-size: 3.5rem; color: #D4AF37; margin-bottom: 20px;">⭐</div>
                <h3 style="color: #333; font-size: 1.4rem; margin-bottom: 15px; font-weight: 500;">Качество</h3>
                <p style="color: #666; line-height: 1.6;">
                    Мы используем только сертифицированные драгоценные металлы и камни высшего качества. 
                    Каждое изделие проходит многоуровневый контроль.
                </p>
            </div>
            
            <div style="background: white; padding: 40px 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; text-align: center; transition: transform 0.3s;">
                <div style="font-size: 3.5rem; color: #D4AF37; margin-bottom: 20px;">🎨</div>
                <h3 style="color: #333; font-size: 1.4rem; margin-bottom: 15px; font-weight: 500;">Уникальность</h3>
                <p style="color: #666; line-height: 1.6;">
                    Все наши изделия создаются в ограниченных коллекциях или являются эксклюзивными 
                    работами. Мы не повторяемся и не создаём масс-маркет.
                </p>
            </div>
            
            <div style="background: white; padding: 40px 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; text-align: center; transition: transform 0.3s;">
                <div style="font-size: 3.5rem; color: #D4AF37; margin-bottom: 20px;">🤝</div>
                <h3 style="color: #333; font-size: 1.4rem; margin-bottom: 15px; font-weight: 500;">Индивидуальный подход</h3>
                <p style="color: #666; line-height: 1.6;">
                    Мы ценим каждого клиента и готовы создать украшение по вашему эскизу, 
                    учитывая все пожелания и особенности.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Процесс создания -->
    <div style="margin-bottom: 80px;">
        <h2 style="color: #8B4513; font-size: 2.5rem; font-weight: 300; text-align: center; margin-bottom: 50px; letter-spacing: 1px;">
            Процесс создания
        </h2>
        
        <div style="position: relative;">
            <!-- Линия процесса -->
            <div style="position: absolute; left: 50px; right: 50px; top: 40px; height: 2px; background: linear-gradient(90deg, #8B4513, #D4AF37, #8B4513); z-index: 1;"></div>
            
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; position: relative; z-index: 2;">
                <?php
                $steps = [
                    ['💡', 'Идея и дизайн', 'Создание эскиза и 3D-модели'],
                    ['✍️', 'Изготовление', 'Ручная работа мастеров-ювелиров'],
                    ['🔍', 'Контроль качества', 'Многоуровневая проверка'],
                    ['📦', 'Упаковка', 'Подарочная упаковка премиум-класса']
                ];
                
                foreach ($steps as $index => $step):
                ?>
                <div style="text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px; position: relative;">
                        <?php echo $step[0]; ?>
                        <div style="position: absolute; top: -5px; right: -5px; width: 30px; height: 30px; background: #D4AF37; color: #333; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1rem;">
                            <?php echo $index + 1; ?>
                        </div>
                    </div>
                    <h3 style="color: #333; font-size: 1.2rem; margin-bottom: 10px; font-weight: 500;"><?php echo $step[1]; ?></h3>
                    <p style="color: #666; font-size: 0.9rem; line-height: 1.5;"><?php echo $step[2]; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Сертификаты и гарантии -->
    <div style="background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%); border-radius: 20px; padding: 60px; margin-bottom: 80px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            <div>
                <h2 style="color: #8B4513; font-size: 2.5rem; font-weight: 300; margin-bottom: 30px; letter-spacing: 1px;">
                    Сертификаты и гарантии
                </h2>
                <p style="color: #333; font-size: 1.1rem; line-height: 1.8; margin-bottom: 25px;">
                    Все изделия Luxury Jewelry сопровождаются официальными сертификатами подлинности, 
                    которые подтверждают качество материалов и соответствие международным стандартам.
                </p>
                
                <ul style="color: #333; font-size: 1.1rem; line-height: 1.8; padding-left: 20px; margin-bottom: 30px;">
                    <li style="margin-bottom: 10px;">Сертификат на драгоценные металлы (проба)</li>
                    <li style="margin-bottom: 10px;">Геммологический сертификат на камни</li>
                    <li style="margin-bottom: 10px;">Пожизненная гарантия на качество изготовления</li>
                    <li>Бесплатное годовое обслуживание</li>
                </ul>
                
                <a href="guarantee.php" 
                   style="display: inline-block; padding: 15px 35px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.3s;">
                    Подробнее о гарантиях
                </a>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div style="font-size: 4rem; color: #D4AF37; margin-bottom: 15px;">📜</div>
                    <h4 style="color: #333; font-size: 1.1rem; margin-bottom: 10px;">Официальные сертификаты</h4>
                    <p style="color: #666; font-size: 0.9rem;">GIA, IGI, HRD</p>
                </div>
                <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div style="font-size: 4rem; color: #D4AF37; margin-bottom: 15px;">🛡️</div>
                    <h4 style="color: #333; font-size: 1.1rem; margin-bottom: 10px;">Пожизненная гарантия</h4>
                    <p style="color: #666; font-size: 0.9rem;">На качество изготовления</p>
                </div>
                <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div style="font-size: 4rem; color: #D4AF37; margin-bottom: 15px;">🔧</div>
                    <h4 style="color: #333; font-size: 1.1rem; margin-bottom: 10px;">Бесплатное обслуживание</h4>
                    <p style="color: #666; font-size: 0.9rem;">В течение 1 года</p>
                </div>
                <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div style="font-size: 4rem; color: #D4AF37; margin-bottom: 15px;">💎</div>
                    <h4 style="color: #333; font-size: 1.1rem; margin-bottom: 10px;">Консультация эксперта</h4>
                    <p style="color: #666; font-size: 0.9rem;">Бесплатные консультации</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Команда -->
    <div style="margin-bottom: 80px;">
        <h2 style="color: #8B4513; font-size: 2.5rem; font-weight: 300; text-align: center; margin-bottom: 50px; letter-spacing: 1px;">
            Наша команда
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            <?php
            $team = [
                ['Анна Петрова', 'Главный дизайнер', '15 лет опыта', 'Окончила ГИТИС, специалист по историческим украшениям'],
                ['Михаил Соколов', 'Ювелир-гравёр', '20 лет опыта', 'Мастер по работе с платиной и белым золотом'],
                ['Елена Ковалёва', 'Геммолог', '12 лет опыта', 'Эксперт по бриллиантам и цветным камням'],
                ['Дмитрий Иванов', 'Директор производства', '18 лет опыта', 'Организует весь процесс создания украшений']
            ];
            
            foreach ($team as $member):
            ?>
            <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f0f0f0;">
                <div style="height: 200px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); display: flex; align-items: center; justify-content: center;">
                    <div style="width: 120px; height: 120px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3.5rem; color: white;">
                        👤
                    </div>
                </div>
                <div style="padding: 25px; text-align: center;">
                    <h3 style="color: #333; font-size: 1.3rem; margin-bottom: 5px; font-weight: 500;"><?php echo $member[0]; ?></h3>
                    <div style="color: #D4AF37; font-size: 1rem; margin-bottom: 10px; font-weight: 500;"><?php echo $member[1]; ?></div>
                    <div style="color: #8B4513; font-size: 0.9rem; margin-bottom: 15px; font-weight: 500;"><?php echo $member[2]; ?></div>
                    <p style="color: #666; font-size: 0.9rem; line-height: 1.5;"><?php echo $member[3]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Контакты -->
<!-- Контакты -->
    <div style="background: white; border-radius: 20px; padding: 60px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); border: 1px solid #e8e8e8;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px;">
            <div>
                <h2 style="color: #8B4513; font-size: 2.5rem; font-weight: 300; margin-bottom: 30px; letter-spacing: 1px;">
                    Контакты
                </h2>
                
                <div style="margin-bottom: 30px;">
                    <h3 style="color: #333; font-size: 1.2rem; margin-bottom: 20px; font-weight: 500;">Главный офис и бутик</h3>
                    <div style="color: #666; font-size: 1.1rem; line-height: 1.6;">
                        <p style="margin-bottom: 15px;">
                            <i class="fas fa-map-marker-alt" style="color: #8B4513; width: 20px; margin-right: 10px;"></i>
                            <?php echo htmlspecialchars($shop_address); ?>
                        </p>
                        <p style="margin-bottom: 15px;">
                            <i class="fas fa-phone-alt" style="color: #8B4513; width: 20px; margin-right: 10px;"></i>
                            <?php echo htmlspecialchars($shop_phone); ?>
                        </p>
                        <p style="margin-bottom: 15px;">
                            <i class="fas fa-envelope" style="color: #8B4513; width: 20px; margin-right: 10px;"></i>
                            <?php echo htmlspecialchars($shop_email); ?>
                        </p>
                    </div>
                </div>
                
                <div>
                    <h3 style="color: #333; font-size: 1.2rem; margin-bottom: 20px; font-weight: 500;">Часы работы</h3>
                    <div style="color: #666; font-size: 1.1rem;">
                        <p style="margin-bottom: 10px;">Пн-Пт: 10:00 - 20:00</p>
                        <p style="margin-bottom: 10px;">Суббота: 11:00 - 19:00</p>
                        <p>Воскресенье: 11:00 - 18:00</p>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 style="color: #333; font-size: 1.2rem; margin-bottom: 20px; font-weight: 500;">Свяжитесь с нами</h3>
                <form onsubmit="alert('Спасибо! Ваше сообщение отправлено.'); return false;">
                    <div style="margin-bottom: 15px;">
                        <input type="text" placeholder="Ваше имя" 
                               style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <input type="email" placeholder="Ваш email" 
                               style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
                    </div>
                    <div style="margin-bottom: 20px;">
                        <textarea placeholder="Ваше сообщение" 
                                  style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; height: 120px; resize: vertical;"></textarea>
                    </div>
                    <button type="submit" 
                            style="width: 100%; padding: 15px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 500; cursor: pointer;">
                        Отправить сообщение
                    </button>
                </form>
                
                <div style="margin-top: 30px;">
                    <h4 style="color: #333; font-size: 1.1rem; margin-bottom: 15px; font-weight: 500;">Мы в социальных сетях</h4>
                    <div style="display: flex; gap: 15px;">
                        <!-- Telegram -->
                        <a href="<?php echo getSetting('telegram_url', '#'); ?>" 
                           style="width: 45px; height: 45px; background: linear-gradient(135deg, #0088cc, #006699); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; text-decoration: none; transition: all 0.3s ease;"
                           title="Telegram">
                            <svg viewBox="0 0 24 24" width="24" height="24" style="fill: currentColor;">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.69 1.03-.59.05-1.03-.39-1.6-.76-.88-.58-1.38-.94-2.23-1.5-.98-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.06-.2-.07-.06-.17-.04-.24-.03-.1.02-1.79 1.14-5.06 3.34-.48.33-.91.5-1.31.49-.43-.01-1.27-.25-1.89-.46-.76-.26-1.37-.4-1.32-.85.03-.27.39-.55 1.07-.84 4.09-1.81 6.82-3.01 8.19-3.6 3.9-1.66 4.71-1.95 5.24-1.96.12 0 .38.03.55.17.14.12.17.28.19.4.01.14.02.46.01.72z"/>
                            </svg>
                        </a>
                        
                        <!-- VK -->
                        <a href="<?php echo getSetting('vk_url', '#'); ?>" 
                           style="width: 45px; height: 45px; background: linear-gradient(135deg, #4a76a8, #2a4a78); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; text-decoration: none; transition: all 0.3s ease;"
                           title="ВКонтакте">
                            <svg viewBox="0 0 24 24" width="24" height="24" style="fill: currentColor;">
                                <path d="M15.07 2H8.93C3.33 2 2 3.33 2 8.93v6.13C2 20.67 3.33 22 8.93 22h6.13c5.61 0 6.94-1.33 6.94-6.94V8.93C22 3.33 20.67 2 15.07 2m.36 14.73h-1.69c-.66 0-.86-.5-2.04-1.58-1.01-.91-1.43-1.02-1.67-1.02-.37 0-.46.1-.46.59v1.43c0 .42-.13.66-1.24.66-1.82 0-3.83-1.1-5.34-3.14-2.02-2.53-2.61-4.44-2.61-4.87 0-.21.1-.42.52-.42h1.69c.39 0 .52.21.67.59.73 1.77 1.95 3.3 2.46 3.3.21 0 .31-.1.31-.67V9.38c-.06-1.2-.69-1.3-.69-1.74 0-.2.16-.4.42-.4h2.61c.35 0 .47.19.47.62v3.35c0 .37.16.49.26.49.21 0 .38-.13.76-.51 1.18-1.24 1.98-3.14 1.98-3.14.1-.22.26-.41.63-.41h1.69c.48 0 .58.25.48.57-.21.76-2.14 3.66-2.14 3.66-.17.26-.24.37 0 .67.17.25.73.75 1.11 1.18.77.71 1.33 1.29 1.49 1.69.14.41.07.83-.38.83z"/>
                            </svg>
                        </a>
                        
                        <!-- Rutube -->
                        <a href="<?php echo getSetting('rutube_url', '#'); ?>" 
                           style="width: 45px; height: 45px; background: black; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; text-decoration: none; transition: all 0.3s ease;"
                           title="Rutube">
                            <svg viewBox="0 0 24 24" width="24" height="24" style="fill: currentColor;">
                                <path d="M20.8,8.6c-0.2-1.5-0.4-2.6-0.6-3.2c-0.2-0.7-0.5-1.3-0.7-1.6c-0.3-0.4-0.7-0.7-1.1-0.9c-0.5-0.2-1.3-0.4-2.4-0.5
                                    c-1.1-0.1-2.6-0.2-4.4-0.2H8.4c-1.8,0-3.3,0.1-4.4,0.2C2.9,3.1,2.1,3.3,1.6,3.5C1.2,3.7,0.9,4,0.6,4.4C0.3,4.7,0.1,5.3,0,6
                                    C0,6.7-0.1,7.7-0.1,9v6c0,1.3,0,2.3,0.1,3c0.1,0.7,0.3,1.3,0.6,1.6c0.3,0.4,0.7,0.7,1.1,0.9c0.5,0.2,1.3,0.4,2.4,0.5
                                    c1.1,0.1,2.6,0.2,4.4,0.2h3.3c1.8,0,3.3-0.1,4.4-0.2c1.1-0.1,1.9-0.3,2.4-0.5c0.5-0.2,0.8-0.5,1.1-0.9c0.3-0.4,0.5-0.9,0.6-1.6
                                    c0.1-0.7,0.2-1.7,0.2-3V9C21,8.1,20.9,8.6,20.8,8.6z M8.8,15.6V8.4l5.6,3.6L8.8,15.6z"/>
                            </svg>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Призыв к действию -->
    <div style="text-align: center; margin-top: 80px; padding: 60px; background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white; border-radius: 20px;">
        <h2 style="font-size: 2.8rem; font-weight: 300; margin-bottom: 20px; letter-spacing: 1px;">
            Создайте свою историю с Luxury Jewelry
        </h2>
        <p style="font-size: 1.2rem; opacity: 0.9; max-width: 700px; margin: 0 auto 40px; line-height: 1.6;">
            Откройте для себя мир эксклюзивных украшений, которые станут частью вашей семьи на долгие годы.
        </p>
        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="catalog.php" 
               style="padding: 18px 45px; background: white; color: #8B4513; text-decoration: none; border-radius: 30px; font-weight: 600; font-size: 1.1rem; transition: all 0.3s;">
                <i class="fas fa-gem"></i> Смотреть коллекцию
            </a>
            <a href="contacts.php" 
               style="padding: 18px 45px; background: transparent; color: white; text-decoration: none; border-radius: 30px; font-weight: 600; font-size: 1.1rem; transition: all 0.3s; border: 2px solid rgba(255, 255, 255, 0.8);">
                <i class="fas fa-map-marker-alt"></i> Посетить бутик
            </a>
        </div>
    </div>
</div>

<script>
// Анимация элементов при скролле
document.addEventListener('DOMContentLoaded', function() {
    // Анимация карточек ценностей
    const valueCards = document.querySelectorAll('div[style*="background: white; padding: 40px 30px"]');
    valueCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.1)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
        });
    });
    
    // Анимация членов команды
    const teamCards = document.querySelectorAll('div[style*="background: white; border-radius: 15px; overflow: hidden"]');
    teamCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const imageSection = this.querySelector('div[style*="height: 200px"]');
            if (imageSection) {
                imageSection.style.transform = 'scale(1.05)';
                imageSection.style.transition = 'transform 0.5s ease';
            }
        });
        card.addEventListener('mouseleave', function() {
            const imageSection = this.querySelector('div[style*="height: 200px"]');
            if (imageSection) {
                imageSection.style.transform = 'scale(1)';
            }
        });
    });
    
    // Счетчики с анимацией
    const counters = document.querySelectorAll('div[style*="font-size: 3rem; color: #D4AF37"]');
    counters.forEach(counter => {
        const target = parseInt(counter.textContent);
        let current = 0;
        const increment = target / 100;
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                counter.textContent = Math.ceil(current) + '+';
                setTimeout(updateCounter, 20);
            } else {
                counter.textContent = target + '+';
            }
        };
        
        // Запускаем анимацию при появлении в поле зрения
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(counter.parentElement);
    });
    
    // Карта (заглушка)
    const mapButton = document.querySelector('button[onclick*="showMap"]');
    if (mapButton) {
        mapButton.addEventListener('click', function() {
            alert('Здесь будет открыта карта с расположением нашего бутика');
        });
    }
    
    // Социальные кнопки
    const socialButtons = document.querySelectorAll('a[style*="width: 45px; height: 45px"]');
    socialButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(5deg)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0)';
        });
    });
});

// Адаптивность
window.addEventListener('resize', function() {
    const aboutGrids = document.querySelectorAll('div[style*="grid-template-columns: 1fr 1fr"]');
    aboutGrids.forEach(grid => {
        if (window.innerWidth < 768) {
            grid.style.gridTemplateColumns = '1fr';
            grid.style.gap = '40px';
        } else {
            grid.style.gridTemplateColumns = '1fr 1fr';
            grid.style.gap = '60px';
        }
    });
    
    const processGrid = document.querySelector('div[style*="grid-template-columns: repeat(4, 1fr)"]');
    if (processGrid) {
        if (window.innerWidth < 768) {
            processGrid.style.gridTemplateColumns = '1fr';
        } else if (window.innerWidth < 1024) {
            processGrid.style.gridTemplateColumns = 'repeat(2, 1fr)';
        } else {
            processGrid.style.gridTemplateColumns = 'repeat(4, 1fr)';
        }
    }
});
</script>

<style>
/* Анимации */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Применяем анимации к секциям */
div[style*="margin-bottom: 80px;"] {
    animation: fadeInUp 0.8s ease-out;
}

/* Адаптивность */
@media (max-width: 768px) {
    div[style*="font-size: 3.5rem"] {
        font-size: 2.5rem !important;
    }
    
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
        gap: 40px !important;
    }
    
    div[style*="padding: 60px"] {
        padding: 30px !important;
    }
    
    div[style*="display: grid; grid-template-columns: repeat(4, 1fr)"] {
        grid-template-columns: 1fr !important;
    }
    
    div[style*="position: absolute; left: 50px; right: 50px; top: 40px;"] {
        display: none !important;
    }
    
    div[style*="width: 80px; height: 80px"] {
        width: 60px !important;
        height: 60px !important;
        font-size: 2rem !important;
    }
}

@media (max-width: 480px) {
    div[style*="font-size: 2.5rem"] {
        font-size: 2rem !important;
    }
    
    div[style*="display: flex; gap: 30px"] {
        flex-direction: column !important;
        gap: 20px !important;
    }
}
</style>

<?php include 'footer.php'; ?>