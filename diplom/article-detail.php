<?php include 'header.php'; ?>

<?php
// Получаем ID статьи из URL
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Массив статей о ювелирных изделиях
$articles = [
    1 => [
        'title' => 'Как выбрать обручальное кольцо: гид по материалам и стилям',
        'category' => 'Советы по выбору',
        'date' => '15.11.2024',
        'read_time' => '6 мин чтения',
        'image' => '💍',
        'content' => '
            <h2>1. Выбор металла</h2>
            <p>Золото, платина или серебро? Каждый металл имеет свои особенности. Золото 585 пробы — классический выбор, платина — более долговечна и гипоаллергенна, серебро — бюджетный вариант с благородным блеском.</p>
            
            <h2>2. Стиль и дизайн</h2>
            <p>Классические гладкие кольца, современные модели с гравировкой или винтажные украшения с филигранью. Выбор зависит от вашего личного стиля и предпочтений.</p>
            
            <h2>3. Размер и посадка</h2>
            <p>Правильный размер — залог комфорта. Учитывайте, что пальцы могут немного отекать в жару или после физической нагрузки.</p>
            
            <h2>4. Камни и инкрустация</h2>
            <p>Бриллианты, сапфиры, изумруды или более доступные цирконы. Выбор камней зависит от бюджета и эстетических предпочтений.</p>
            
            <h2>5. Сертификаты и гарантии</h2>
            <p>Всегда проверяйте наличие сертификатов на драгоценные металлы и камни. Качественное ювелирное изделие всегда имеет документальное подтверждение.</p>
        '
    ],
    2 => [
        'title' => 'Уход за ювелирными изделиями: сохраняем блеск на долгие годы',
        'category' => 'Уход и хранение',
        'date' => '10.11.2024',
        'read_time' => '8 мин чтения',
        'image' => '✨',
        'content' => '
            <h2>Ежедневный уход</h2>
            <p>Регулярная чистка мягкой щеткой и специальными растворами поможет сохранить блеск ваших украшений.</p>
            
            <h2>Правила хранения</h2>
            <p>Храните каждое изделие отдельно в мягких мешочках или специальных шкатулках с отделениями, чтобы избежать царапин.</p>
        '
    ]
];

// Получаем данные статьи
$article = $articles[$article_id] ?? $articles[1];
?>

<div class="main-content">
    <!-- Article Detail Section -->
    <section class="article-detail-section">
        <div class="article-detail-container">
            <!-- Back Button -->
            <a href="articles.php" class="back-button">← К списку статей</a>
            
            <!-- Article Header -->
            <div class="article-detail-header">
                <div class="article-detail-category"><?php echo $article['category']; ?></div>
                <h1 class="article-detail-title"><?php echo $article['title']; ?></h1>
                <div class="article-detail-meta">
                    <span class="meta-item">📅 <?php echo $article['date']; ?></span>
                    <span class="meta-item">⏱️ <?php echo $article['read_time']; ?></span>
                    <span class="meta-item">👁️ 3.2K просмотров</span>
                </div>
            </div>

            <!-- Article Image -->
            <div class="article-detail-image">
                <div class="detail-image-placeholder"><?php echo $article['image']; ?></div>
            </div>

            <!-- Article Content -->
            <div class="article-detail-content">
                <?php echo $article['content']; ?>
            </div>

            <!-- Author Info -->
            <div class="author-card">
                <div class="author-avatar">💎</div>
                <div class="author-info">
                    <h4 class="author-name">Анна Соколова</h4>
                    <p class="author-bio">Сертифицированный геммолог с 10-летним опытом. Эксперт по ювелирным изделиям и драгоценным камням.</p>
                </div>
            </div>

            <!-- Tags -->
            <div class="article-tags">
                <span class="tag-label">Теги:</span>
                <a href="#" class="tag">кольца</a>
                <a href="#" class="tag">обручальные</a>
                <a href="#" class="tag">золото</a>
                <a href="#" class="tag">бриллианты</a>
                <a href="#" class="tag">выбор</a>
            </div>

            <!-- Social Share -->
            <div class="social-share">
                <span class="share-label">Поделиться:</span>
                <button class="share-btn vk">ВКонтакте</button>
                <button class="share-btn telegram">Telegram</button>
                <button class="share-btn copy">Копировать ссылку</button>
            </div>

            <!-- Related Articles -->
            <div class="related-articles">
                <h3 class="related-title">Читайте также</h3>
                <div class="related-grid">
                    <a href="article-detail.php?id=2" class="related-article">
                        <div class="related-image">
                            <div class="related-image-placeholder">✨</div>
                        </div>
                        <h4>Уход за ювелирными изделиями</h4>
                        <span class="related-category">Уход и хранение</span>
                    </a>
                    
                    <a href="article-detail.php?id=3" class="related-article">
                        <div class="related-image">
                            <div class="related-image-placeholder">📿</div>
                        </div>
                        <h4>Модные тенденции 2024</h4>
                        <span class="related-category">Мода и стиль</span>
                    </a>
                    
                    <a href="article-detail.php?id=4" class="related-article">
                        <div class="related-image">
                            <div class="related-image-placeholder">🔬</div>
                        </div>
                        <h4>Как отличить подделку</h4>
                        <span class="related-category">Безопасность</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Article Detail Styles - Luxury Theme */
.article-detail-section {
    padding: 0 20px;
    max-width: 800px;
    margin: 0 auto 60px;
}

.article-detail-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.05);
    border: 1px solid #e8e8e8;
}

/* Back Button */
.back-button {
    display: inline-block;
    color: #8B4513;
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 30px;
    transition: color 0.3s ease;
}

.back-button:hover {
    color: #D4AF37;
}

/* Article Header */
.article-detail-header {
    margin-bottom: 30px;
}

.article-detail-category {
    display: inline-block;
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.article-detail-title {
    font-size: 2.2rem;
    color: #333;
    margin-bottom: 20px;
    line-height: 1.3;
    font-weight: 300;
}

.article-detail-meta {
    display: flex;
    gap: 20px;
    color: #666;
    font-size: 0.9rem;
}

/* Article Image */
.article-detail-image {
    height: 300px;
    background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);
    border-radius: 15px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e8e8e8;
}

.detail-image-placeholder {
    font-size: 6rem;
    opacity: 0.8;
}

/* Article Content */
.article-detail-content {
    margin-bottom: 40px;
}

.article-detail-content h2 {
    color: #8B4513;
    margin: 30px 0 15px;
    font-size: 1.5rem;
    font-weight: 400;
    border-bottom: 2px solid #D4AF37;
    padding-bottom: 5px;
}

.article-detail-content p {
    color: #555;
    line-height: 1.7;
    margin-bottom: 20px;
    font-size: 1.05rem;
}

/* Author Card */
.author-card {
    display: flex;
    gap: 20px;
    padding: 25px;
    background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);
    border-radius: 15px;
    margin-bottom: 30px;
    border: 1px solid #e8e8e8;
}

.author-avatar {
    font-size: 3rem;
}

.author-info {
    flex: 1;
}

.author-name {
    font-size: 1.2rem;
    color: #8B4513;
    margin-bottom: 10px;
    font-weight: 400;
}

.author-bio {
    color: #666;
    line-height: 1.5;
}

/* Tags */
.article-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    margin-bottom: 30px;
}

.tag-label {
    color: #666;
    font-weight: 600;
}

.tag {
    display: inline-block;
    padding: 6px 15px;
    background: #f5f5f5;
    color: #555;
    text-decoration: none;
    border-radius: 15px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.tag:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
}

/* Social Share */
.social-share {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
    margin-bottom: 40px;
}

.share-label {
    color: #666;
    font-weight: 600;
}

.share-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.share-btn.vk {
    background: #0077ff;
    color: white;
}

.share-btn.telegram {
    background: #0088cc;
    color: white;
}

.share-btn.copy {
    background: #f5f5f5;
    color: #333;
}

.share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Related Articles */
.related-articles {
    margin-bottom: 50px;
}

.related-title {
    font-size: 1.5rem;
    color: #8B4513;
    margin-bottom: 25px;
    font-weight: 300;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.related-article {
    display: block;
    text-decoration: none;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
    border: 1px solid #e8e8e8;
}

.related-article:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.related-image {
    height: 150px;
    background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.related-image-placeholder {
    font-size: 3rem;
    opacity: 0.8;
}

.related-article h4 {
    padding: 15px 15px 5px;
    color: #333;
    font-size: 1.1rem;
    line-height: 1.4;
    font-weight: 400;
}

.related-category {
    display: inline-block;
    padding: 5px 10px;
    margin: 0 15px 15px;
    background: #f5f5f5;
    color: #666;
    border-radius: 12px;
    font-size: 0.8rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .article-detail-section {
        padding: 0 15px;
    }
    
    .article-detail-container {
        padding: 25px;
    }
    
    .article-detail-title {
        font-size: 1.8rem;
    }
    
    .article-detail-meta {
        flex-direction: column;
        gap: 10px;
    }
    
    .article-detail-image {
        height: 200px;
    }
    
    .detail-image-placeholder {
        font-size: 4rem;
    }
    
    .author-card {
        flex-direction: column;
        text-align: center;
    }
    
    .social-share {
        justify-content: center;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Social share functionality
document.querySelectorAll('.share-btn').forEach(button => {
    button.addEventListener('click', function() {
        const platform = this.classList[1];
        const url = window.location.href;
        const title = document.querySelector('.article-detail-title').textContent;
        
        switch(platform) {
            case 'vk':
                window.open(`https://vk.com/share.php?url=${encodeURIComponent(url)}&title=${encodeURIComponent(title)}`, '_blank');
                break;
            case 'telegram':
                window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`, '_blank');
                break;
            case 'copy':
                navigator.clipboard.writeText(url).then(() => {
                    alert('Ссылка скопирована в буфер обмена!');
                });
                break;
        }
    });
});
</script>

<?php include 'footer.php'; ?>