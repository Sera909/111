<?php include 'header.php'; ?>

<div class="main-content">
    <!-- Hero Section for Articles -->
    <section class="articles-hero">
        <div class="articles-hero-content">
            <h1 class="articles-hero-title">Luxury Journal</h1>
            <p class="articles-hero-subtitle">Экспертные статьи о ювелирных изделиях, трендах и уходе</p>
        </div>
    </section>

    <!-- Articles Section -->
    <section class="articles-section">
        <div class="articles-container">
            <!-- Categories Filter -->
            <div class="categories-filter">
                <h3 class="filter-title">Категории статей</h3>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-category="all">Все статьи</button>
                    <button class="filter-btn" data-category="selection">Выбор изделий</button>
                    <button class="filter-btn" data-category="care">Уход и хранение</button>
                    <button class="filter-btn" data-category="trends">Модные тенденции</button>
                    <button class="filter-btn" data-category="gemstones">Драгоценные камни</button>
                    <button class="filter-btn" data-category="history">История ювелирного искусства</button>
                </div>
            </div>

            <!-- Search Box -->
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Поиск статей...">
                <button class="search-btn">🔍</button>
            </div>

            <!-- Articles Grid -->
            <div class="articles-main-grid">
                <!-- Article 1 -->
                <article class="article-main-card" data-category="selection">
                    <div class="article-main-image">
                        <div class="article-badge">Популярное</div>
                        <div class="image-main-placeholder">💍</div>
                    </div>
                    <div class="article-main-content">
                        <span class="article-main-category">Выбор изделий</span>
                        <h3 class="article-main-title">Как выбрать обручальное кольцо: гид по материалам и стилям</h3>
                        <p class="article-main-excerpt">Полное руководство по выбору обручальных колец. От выбора металла и камней до определения размера и стиля. Экспертные советы от геммолога.</p>
                        <div class="article-main-meta">
                            <div class="meta-left">
                                <span class="article-main-date">📅 15.11.2024</span>
                                <span class="article-main-read">⏱️ 6 мин чтения</span>
                            </div>
                            <a href="article-detail.php?id=1" class="article-main-link">Читать полностью →</a>
                        </div>
                    </div>
                </article>

                <!-- Article 2 -->
                <article class="article-main-card" data-category="care">
                    <div class="article-main-image">
                        <div class="article-badge">Новое</div>
                        <div class="image-main-placeholder">✨</div>
                    </div>
                    <div class="article-main-content">
                        <span class="article-main-category">Уход и хранение</span>
                        <h3 class="article-main-title">Уход за ювелирными изделиями: сохраняем блеск на долгие годы</h3>
                        <p class="article-main-excerpt">Правила ухода за золотом, серебром и изделиями с драгоценными камнями. Домашние и профессиональные методы чистки.</p>
                        <div class="article-main-meta">
                            <div class="meta-left">
                                <span class="article-main-date">📅 10.11.2024</span>
                                <span class="article-main-read">⏱️ 8 мин чтения</span>
                            </div>
                            <a href="article-detail.php?id=2" class="article-main-link">Читать полностью →</a>
                        </div>
                    </div>
                </article>

                <!-- Article 3 -->
                <article class="article-main-card" data-category="trends">
                    <div class="article-main-image">
                        <div class="image-main-placeholder">📿</div>
                    </div>
                    <div class="article-main-content">
                        <span class="article-main-category">Модные тенденции</span>
                        <h3 class="article-main-title">Тенденции 2024: что носить этой весной</h3>
                        <p class="article-main-excerpt">Обзор самых актуальных ювелирных трендов нового сезона. От минималистичных серег до statement-колье.</p>
                        <div class="article-main-meta">
                            <div class="meta-left">
                                <span class="article-main-date">📅 05.11.2024</span>
                                <span class="article-main-read">⏱️ 7 мин чтения</span>
                            </div>
                            <a href="article-detail.php?id=3" class="article-main-link">Читать полностью →</a>
                        </div>
                    </div>
                </article>

                <!-- Article 4 -->
                <article class="article-main-card" data-category="gemstones">
                    <div class="article-main-image">
                        <div class="image-main-placeholder">💎</div>
                    </div>
                    <div class="article-main-content">
                        <span class="article-main-category">Драгоценные камни</span>
                        <h3 class="article-main-title">Бриллианты: 4C критерия качества</h3>
                        <p class="article-main-excerpt">Подробный разбор системы оценки бриллиантов: цвет, чистота, огранка и карат. Как выбрать идеальный камень.</p>
                        <div class="article-main-meta">
                            <div class="meta-left">
                                <span class="article-main-date">📅 01.11.2024</span>
                                <span class="article-main-read">⏱️ 9 мин чтения</span>
                            </div>
                            <a href="article-detail.php?id=4" class="article-main-link">Читать полностью →</a>
                        </div>
                    </div>
                </article>

                <!-- Article 5 -->
                <article class="article-main-card" data-category="history">
                    <div class="article-main-image">
                        <div class="image-main-placeholder">🏺</div>
                    </div>
                    <div class="article-main-content">
                        <span class="article-main-category">История ювелирного искусства</span>
                        <h3 class="article-main-title">Ар-деко в ювелирном искусстве: наследие 20-х годов</h3>
                        <p class="article-main-excerpt">Влияние стиля ар-деко на современные ювелирные украшения. Геометрические формы и смелые цветовые решения.</p>
                        <div class="article-main-meta">
                            <div class="meta-left">
                                <span class="article-main-date">📅 28.10.2024</span>
                                <span class="article-main-read">⏱️ 10 мин чтения</span>
                            </div>
                            <a href="article-detail.php?id=5" class="article-main-link">Читать полностью →</a>
                        </div>
                    </div>
                </article>

                <!-- Article 6 -->
                <article class="article-main-card" data-category="selection">
                    <div class="article-main-image">
                        <div class="image-main-placeholder">👑</div>
                    </div>
                    <div class="article-main-content">
                        <span class="article-main-category">Выбор изделий</span>
                        <h3 class="article-main-title">Как подобрать украшения к форме лица</h3>
                        <p class="article-main-excerpt">Экспертные советы по выбору серег и колье в зависимости от формы лица. Визуальные примеры и рекомендации.</p>
                        <div class="article-main-meta">
                            <div class="meta-left">
                                <span class="article-main-date">📅 25.10.2024</span>
                                <span class="article-main-read">⏱️ 5 мин чтения</span>
                            </div>
                            <a href="article-detail.php?id=6" class="article-main-link">Читать полностью →</a>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <a href="#" class="page-btn active">1</a>
                <a href="#" class="page-btn next">Далее →</a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="newsletter-container">
            <h3 class="newsletter-title">💎 Подпишитесь на Luxury Digest</h3>
            <p class="newsletter-text">Получайте эксклюзивные статьи о ювелирных трендах и специальные предложения</p>
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Ваш email" required>
                <button type="submit" class="newsletter-btn">Подписаться</button>
            </form>
        </div>
    </section>
</div>

<?php include 'footer.php'; ?>

<style>
/* Articles Page Styles - Luxury Theme */
.articles-hero {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    padding: 80px 20px;
    text-align: center;
    border-radius: 20px;
    margin: 20px 20px 40px;
    position: relative;
    overflow: hidden;
}

.articles-hero:before {
    content: "💎✨💍";
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 2rem;
    opacity: 0.3;
}

.articles-hero-content {
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.articles-hero-title {
    font-size: 3rem;
    font-weight: 300;
    margin-bottom: 15px;
    letter-spacing: 2px;
}

.articles-hero-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    font-weight: 300;
}

.articles-section {
    padding: 0 20px;
    max-width: 1200px;
    margin: 0 auto 60px;
}

.articles-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.05);
    border: 1px solid #e8e8e8;
}

/* Categories Filter */
.categories-filter {
    margin-bottom: 40px;
}

.filter-title {
    font-size: 1.3rem;
    color: #8B4513;
    margin-bottom: 20px;
    font-weight: 400;
}

.filter-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 10px 20px;
    background: #f5f5f5;
    border: 1px solid #e8e8e8;
    border-radius: 25px;
    cursor: pointer;
    font-size: 0.95rem;
    color: #555;
    transition: all 0.3s ease;
}

.filter-btn:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border-color: transparent;
}

.filter-btn.active {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border-color: transparent;
}

/* Search Box */
.search-box {
    display: flex;
    margin-bottom: 40px;
}

.search-input {
    flex: 1;
    padding: 15px 20px;
    border: 1px solid #e8e8e8;
    border-radius: 10px 0 0 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f9f9f9;
}

.search-input:focus {
    outline: none;
    border-color: #D4AF37;
    background: white;
}

.search-btn {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border: none;
    padding: 15px 25px;
    border-radius: 0 10px 10px 0;
    cursor: pointer;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.search-btn:hover {
    background: linear-gradient(135deg, #A0522D 0%, #D4AF37 100%);
}

/* Articles Grid */
.articles-main-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

.article-main-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #e8e8e8;
}

.article-main-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.article-main-image {
    height: 200px;
    background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.article-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #D4AF37;
    color: white;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.image-main-placeholder {
    font-size: 4rem;
    opacity: 0.8;
}

.article-main-content {
    padding: 25px;
}

.article-main-category {
    display: inline-block;
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.article-main-title {
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 12px;
    line-height: 1.4;
    font-weight: 400;
}

.article-main-excerpt {
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
    font-size: 0.95rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-main-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.meta-left {
    display: flex;
    gap: 15px;
}

.article-main-date,
.article-main-read {
    color: #888;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.article-main-link {
    color: #8B4513;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.article-main-link:hover {
    color: #D4AF37;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 40px;
}

.page-btn {
    display: inline-block;
    padding: 10px 18px;
    background: #f5f5f5;
    color: #555;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
    border: 1px solid #e8e8e8;
}

.page-btn:hover {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border-color: transparent;
}

.page-btn.active {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border-color: transparent;
}

.page-btn.next {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border-color: transparent;
}

.page-btn.next:hover {
    background: linear-gradient(135deg, #A0522D 0%, #D4AF37 100%);
}

/* Newsletter Section */
.newsletter-section {
    background: linear-gradient(135deg, #f8f0e3 0%, #f5e6d3 100%);
    border-radius: 20px;
    margin: 40px 20px;
    padding: 50px 40px;
    border: 1px solid #e8e8e8;
}

.newsletter-container {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}

.newsletter-title {
    font-size: 1.8rem;
    color: #8B4513;
    margin-bottom: 15px;
    font-weight: 300;
}

.newsletter-text {
    color: #666;
    margin-bottom: 25px;
    font-size: 1.1rem;
}

.newsletter-form {
    display: flex;
    gap: 10px;
    max-width: 500px;
    margin: 0 auto;
}

.newsletter-input {
    flex: 1;
    padding: 15px 20px;
    border: 1px solid #d4b89c;
    border-radius: 10px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    background: white;
}

.newsletter-input:focus {
    outline: none;
    border-color: #8B4513;
}

.newsletter-btn {
    background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%);
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.newsletter-btn:hover {
    background: linear-gradient(135deg, #A0522D 0%, #D4AF37 100%);
}

/* Responsive Design */
@media (max-width: 768px) {
    .articles-hero {
        margin: 10px 10px 30px;
        padding: 50px 15px;
    }
    
    .articles-hero-title {
        font-size: 2rem;
    }
    
    .articles-section {
        padding: 0 15px;
    }
    
    .articles-container {
        padding: 25px;
    }
    
    .filter-buttons {
        justify-content: center;
    }
    
    .search-box {
        flex-direction: column;
    }
    
    .search-input {
        border-radius: 10px;
        margin-bottom: 10px;
    }
    
    .search-btn {
        border-radius: 10px;
        padding: 12px;
    }
    
    .articles-main-grid {
        grid-template-columns: 1fr;
    }
    
    .article-main-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .meta-left {
        flex-direction: column;
        gap: 5px;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
    
    .newsletter-section {
        margin: 20px 10px;
        padding: 30px 20px;
    }
    
    .newsletter-form {
        flex-direction: column;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .articles-main-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
// Filter articles by category
document.querySelectorAll('.filter-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        this.classList.add('active');
        
        const category = this.dataset.category;
        const articles = document.querySelectorAll('.article-main-card');
        
        articles.forEach(article => {
            if (category === 'all' || article.dataset.category === category) {
                article.style.display = 'block';
            } else {
                article.style.display = 'none';
            }
        });
    });
});

// Search functionality
const searchInput = document.querySelector('.search-input');
const searchBtn = document.querySelector('.search-btn');

searchBtn.addEventListener('click', function() {
    performSearch();
});

searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

function performSearch() {
    const searchTerm = searchInput.value.toLowerCase();
    const articles = document.querySelectorAll('.article-main-card');
    
    articles.forEach(article => {
        const title = article.querySelector('.article-main-title').textContent.toLowerCase();
        const excerpt = article.querySelector('.article-main-excerpt').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || excerpt.includes(searchTerm)) {
            article.style.display = 'block';
        } else {
            article.style.display = 'none';
        }
    });
}

// Newsletter form submission
const newsletterForm = document.querySelector('.newsletter-form');
newsletterForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('.newsletter-input').value;
    
    // Here you would typically send the email to your server
    alert(`Спасибо за подписку на Luxury Digest! На адрес ${email} будут приходить эксклюзивные материалы.`);
    this.reset();
});
</script>