<?php
session_start();

$host = 'localhost';
$dbname = 'agorafrancia';
$user = 'root';
$pass = 'root'; 

$totalItems = 0;
$totalVendeurs = 0;
$totalAcheteurs = 0;
$categoriesCounts = [];

//connexion base de donnée
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //Compter le nb article en vente
    $stmt = $pdo->query("SELECT COUNT(*) FROM items");
    $totalItems = $stmt->fetchColumn();
    
    //compter le nb de vendeurs actifs
    $stmt = $pdo->query("SELECT COUNT(*) FROM vendeurs");
    $totalVendeurs = $stmt->fetchColumn();
    
    //compter le nb d'acheteurs
    $stmt = $pdo->query("SELECT COUNT(*) FROM acheteurs");
    $totalAcheteurs = $stmt->fetchColumn();
    
    //compter le nb d'articles par catégorie
    $stmt = $pdo->query("SELECT Catégorie_item, COUNT(*) as count FROM items WHERE Catégorie_item IS NOT NULL GROUP BY Catégorie_item");
    $categoriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // trier données par catégorie
    foreach ($categoriesData as $row) {
        $categoriesCounts[$row['Catégorie_item']] = (int)$row['count'];
    }
    
    //récupérer articles populaires aléatoires pour chaque catégorie
    $popularItemsByCategory = [];
    $categories = ['Mode & Accessoires', 'Sports & Loisirs', 'Livres & Multimédia', 'Antiquités & Collection', 'Art & Artisanat', 'Bijoux & Montres'];
    
    foreach ($categories as $category) {
        $stmt = $pdo->prepare("SELECT Nom_item FROM items WHERE Catégorie_item = ? AND Nom_item IS NOT NULL ORDER BY RAND() LIMIT 5");
        $stmt->execute([$category]);
        $items = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        //items par défaut si moins de 5 articles 
        if (count($items) < 5) {
            $defaultItems = [
                'Mode & Accessoires' => ['Nike', 'Adidas', 'Zara', 'H&M', 'Louis Vuitton'],
                'Sports & Loisirs' => ['Football', 'Vélo', 'Ski', 'Tennis', 'Fitness'],
                'Livres & Multimédia' => ['Harry Potter', 'Manga', 'Histoire', 'Romans', 'Sciences'],
                'Antiquités & Collection' => ['Louis XVI', 'Art Déco', 'Vintage', 'Porcelaine', 'Horlogerie'],
                'Art & Artisanat' => ['Peinture', 'Sculpture', 'Céramique', 'Photographie', 'Artisanat'],
                'Bijoux & Montres' => ['Rolex', 'Cartier', 'Or', 'Diamant', 'Argent']
            ];
            
            $needed = 5 - count($items);
            $defaultForCategory = $defaultItems[$category] ?? ['Article 1', 'Article 2', 'Article 3', 'Article 4', 'Article 5'];
            $items = array_merge($items, array_slice($defaultForCategory, 0, $needed));
        }
        
        $popularItemsByCategory[$category] = array_slice($items, 0, 5);
    }
    
    echo "<!-- DEBUG: Articles en vente: $totalItems -->";
    echo "<!-- DEBUG: Vendeurs actifs: $totalVendeurs -->";
    echo "<!-- DEBUG: Acheteurs: $totalAcheteurs -->";
    echo "<!-- DEBUG: Catégories trouvées: " . print_r($categoriesCounts, true) . " -->";
    echo "<!-- DEBUG: Articles populaires: " . print_r($popularItemsByCategory, true) . " -->";
    
} catch (PDOException $e) {
    echo "<!-- DEBUG: Erreur de connexion BDD: " . $e->getMessage() . " -->";
    // si erreur valeurs par défaut
    $categoriesCounts = [
        'Mode & Accessoires' => 0,
        'Sports & Loisirs' => 0,
        'Livres & Multimédia' => 0,
        'Antiquités & Collection' => 0,
        'Art & Artisanat' => 0,
        'Bijoux & Montres' => 0
    ];
    
    $popularItemsByCategory = [
        'Mode & Accessoires' => ['Nike', 'Adidas', 'Zara', 'H&M', 'Louis Vuitton'],
        'Sports & Loisirs' => ['Football', 'Vélo', 'Ski', 'Tennis', 'Fitness'],
        'Livres & Multimédia' => ['Harry Potter', 'Manga', 'Histoire', 'Romans', 'Sciences'],
        'Antiquités & Collection' => ['Louis XVI', 'Art Déco', 'Vintage', 'Porcelaine', 'Horlogerie'],
        'Art & Artisanat' => ['Peinture', 'Sculpture', 'Céramique', 'Photographie', 'Artisanat'],
        'Bijoux & Montres' => ['Rolex', 'Cartier', 'Or', 'Diamant', 'Argent']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Catégories - AGORA FRANCIA</title>
        <link rel="stylesheet" href="styles.css" />
        <link rel="icon" href="logo.ico" type="image/x-icon" />
        <script src="script.js" defer></script>
        <style>
            .categories-page-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 40px 20px;
            }
            
            .page-header {
                text-align: center;
                margin-bottom: 50px;
                padding: 40px 20px;
                background: linear-gradient(135deg, #397194 0%, #252d70 100%);
                color: white;
                border-radius: 15px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                position: relative;
                overflow: hidden;
            }
            
            .page-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="categories" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23categories)"/></svg>') repeat;
                opacity: 0.3;
                z-index: 1;
            }
            
            .page-header .container {
                position: relative;
                z-index: 2;
            }
            
            .page-header h1 {
                font-size: 36px;
                margin-bottom: 15px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
            }
            
            .page-header p {
                font-size: 18px;
                opacity: 0.9;
                margin: 0;
                max-width: 600px;
                margin: 0 auto;
                line-height: 1.6;
            }
            
            .search-categories {
                background: white;
                border-radius: 15px;
                padding: 25px;
                margin-bottom: 40px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                border-left: 4px solid #397194;
            }
            
            .search-categories h2 {
                color: #397194;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 20px;
            }
            
            .categories-search-bar {
                display: flex;
                gap: 15px;
                margin-bottom: 20px;
            }
            
            .search-input {
                flex: 1;
                padding: 12px 20px;
                border: 2px solid #e0e0e0;
                border-radius: 10px;
                font-size: 16px;
                transition: all 0.3s ease;
            }
            
            .search-input:focus {
                outline: none;
                border-color: #397194;
                box-shadow: 0 0 0 3px rgba(57, 113, 148, 0.1);
            }
            
            .search-btn {
                padding: 12px 25px;
                background: #397194;
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .search-btn:hover {
                background: #252d70;
                transform: translateY(-2px);
            }
            
            .filter-tags {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            
            .filter-tag {
                padding: 6px 16px;
                background: rgba(57, 113, 148, 0.1);
                color: #397194;
                border: 2px solid transparent;
                border-radius: 20px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .filter-tag:hover {
                background: #397194;
                color: white;
                transform: translateY(-2px);
            }
            
            .filter-tag.active {
                background: #397194;
                color: white;
                border-color: #252d70;
            }
            
            .categories-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin-bottom: 40px;
            }
            
            .stat-card {
                background: white;
                padding: 25px;
                border-radius: 15px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                text-align: center;
                transition: all 0.3s ease;
                border-top: 4px solid #397194;
            }
            
            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            }
            
            .stat-number {
                font-size: 32px;
                font-weight: bold;
                color: #397194;
                display: block;
                margin-bottom: 8px;
            }
            
            .stat-label {
                color: #666;
                font-size: 16px;
                font-weight: 500;
            }
            
            .categories-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 25px;
                margin-bottom: 40px;
            }
            
            .category-card {
                background: white;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                cursor: pointer;
                position: relative;
                border: 2px solid transparent;
            }
            
            .category-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 30px rgba(0,0,0,0.2);
                border-color: #397194;
            }
            
            .category-header {
                background: linear-gradient(135deg, var(--category-color, #397194), var(--category-color-dark, #252d70));
                color: white;
                padding: 25px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            
            .category-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>') repeat;
                opacity: 0.3;
            }
            
            .category-icon {
                font-size: 48px;
                margin-bottom: 15px;
                display: block;
                position: relative;
                z-index: 2;
            }
            
            .category-title {
                font-size: 22px;
                font-weight: bold;
                margin-bottom: 8px;
                position: relative;
                z-index: 2;
            }
            
            .category-subtitle {
                font-size: 14px;
                opacity: 0.9;
                position: relative;
                z-index: 2;
            }
            
            .category-content {
                padding: 25px;
            }
            
            .category-description {
                color: #666;
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            
            .category-stats {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
            }
            
            .category-count {
                font-size: 18px;
                font-weight: bold;
                color: #397194;
            }
            
            .category-trend {
                font-size: 12px;
                color: #28a745;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .popular-items {
                margin-bottom: 20px;
            }
            
            .popular-items-title {
                font-size: 14px;
                font-weight: 600;
                color: #333;
                margin-bottom: 10px;
            }
            
            .popular-items-list {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            
            .popular-tag {
                padding: 4px 10px;
                background: rgba(57, 113, 148, 0.1);
                color: #397194;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
            }
            
            .category-actions {
                display: flex;
                gap: 10px;
            }
            
            .category-btn {
                flex: 1;
                padding: 12px;
                border: none;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }
            
            .btn-primary {
                background: #397194;
                color: white;
            }
            
            .btn-primary:hover {
                background: #252d70;
                transform: translateY(-2px);
                color: white;
            }
            
            .btn-secondary {
                background: transparent;
                color: #397194;
                border: 2px solid #397194;
            }
            
            .btn-secondary:hover {
                background: #397194;
                color: white;
            }
            
            .trending-section {
                background: white;
                border-radius: 15px;
                padding: 30px;
                margin-bottom: 40px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                border-left: 4px solid #ff6b6b;
            }
            
            .trending-title {
                color: #ff6b6b;
                font-size: 24px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .trending-categories {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
            }
            
            .trending-item {
                padding: 20px;
                background: rgba(255, 107, 107, 0.05);
                border-radius: 12px;
                border: 2px solid rgba(255, 107, 107, 0.2);
                text-align: center;
                transition: all 0.3s ease;
                cursor: pointer;
            }
            
            .trending-item:hover {
                background: rgba(255, 107, 107, 0.1);
                border-color: #ff6b6b;
                transform: scale(1.05);
            }
            
            .trending-icon {
                font-size: 32px;
                margin-bottom: 10px;
                display: block;
            }
            
            .trending-name {
                font-weight: 600;
                color: #333;
                margin-bottom: 5px;
            }
            
            .trending-growth {
                color: #ff6b6b;
                font-size: 12px;
                font-weight: 600;
            }
            
            .no-results {
                text-align: center;
                padding: 60px 20px;
                color: #666;
            }
            
            .no-results-icon {
                font-size: 64px;
                margin-bottom: 20px;
                opacity: 0.5;
            }
            
            .no-results h3 {
                font-size: 24px;
                margin-bottom: 10px;
                color: #333;
            }
            
            .no-results p {
                font-size: 16px;
                margin-bottom: 20px;
                line-height: 1.6;
            }
            
            .reset-filters-btn {
                background: #397194;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s ease;
            }
            
            .reset-filters-btn:hover {
                background: #252d70;
                transform: translateY(-2px);
            }

            .category-card[data-category="electronics"] {
                --category-color: #007bff;
                --category-color-dark: #0056b3;
            }
            
            .category-card[data-category="fashion"] {
                --category-color: #e83e8c;
                --category-color-dark: #c42760;
            }
            
            .category-card[data-category="home"] {
                --category-color: #28a745;
                --category-color-dark: #1e7e34;
            }
            
            .category-card[data-category="sports"] {
                --category-color: #fd7e14;
                --category-color-dark: #d15c08;
            }
            
            .category-card[data-category="vehicles"] {
                --category-color: #6f42c1;
                --category-color-dark: #563e8a;
            }
            
            .category-card[data-category="books"] {
                --category-color: #20c997;
                --category-color-dark: #1aa179;
            }
            
            .category-card[data-category="antiques"] {
                --category-color: #8b4513;
                --category-color-dark: #6b3410;
            }
            
            .category-card[data-category="art"] {
                --category-color: #9b59b6;
                --category-color-dark: #8e44ad;
            }
            
            .category-card[data-category="jewelry"] {
                --category-color: #f39c12;
                --category-color-dark: #d68910;
            }
            
            .category-card[data-category="toys"] {
                --category-color: #ff6b6b;
                --category-color-dark: #ee5a52;
            }
        </style>
    </head>
    <body>
        <header class="header">
            <div class="header-top">
                <div class="header-top-content">
                    <div class="header-top-left">
                        🏠 Toute la France
                    </div>
                    <div class="header-top-right">
                        <a href="mentions.html">Mentions légales</a>
                        <a href="conf.html">Politique de Confidentialité</a>
                    </div>
                </div>
            </div>
            <div class="header-main">
                <div class="header-main-content">
                    <div class="burger-container">
                        <button class="header-burger" onclick="toggleHeaderDropdown()">☰Menu</button>
                        <div class="header-dropdown" id="headerDropdown">
                            <ul>
                                <li><a href="accueil.php"><span class="menu-icon">🏠</span>Accueil</a></li>
                                <li><a href="toutparcourir.php"><span class="menu-icon">🔍</span>Tout parcourir</a></li>
                                <li><a href="notification.php"><span class="menu-icon">🔔</span>Notifications</a></li>
                                <li><a href="panier.php"><span class="menu-icon">🛒</span>Panier</a></li>
                                <li><a href="redirection.php"><span class="menu-icon">👤</span>Votre compte</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="logo-section">
                        <a href="accueil.php">
                            <img src="logo2.jpeg" alt="Logo Agora Francia" class="logo"/>
                        </a>
                        <h1>AGORA FRANCIA</h1>
                    </div>
                    <div class="search-container">
                        <form class="search-form">
                            <input type="text" class="search-input" placeholder="Que recherchez-vous ?">
                            <input type="text" class="location-input" placeholder="Où ?">
                            <button type="submit" class="search-btn">🔍</button>
                        </form>
                    </div>
                    <div class="header-actions">
                        <?php if (isset($_SESSION['username'])): ?>
                            <span style="margin-right: 15px; color: #397194; font-weight: 500;">
                                👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                            <a href="logout.php" class="auth-btn danger">Se déconnecter</a>
                        <?php else: ?>
                            <a href="inscription.php" class="auth-btn">S'inscrire</a>
                            <a href="connexion.php" class="auth-btn primary">Se connecter</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <nav class="nav-bar">
                <div class="nav-content">
                    <ul class="nav-menu">
                        <li><a href="accueil.php">Accueil</a></li>
                        <li><a href="toutparcourir.php">Tout parcourir</a></li>
                        <li><a href="notification.php">Notifications</a></li>
                        <li><a href="panier.php">Panier</a></li>
                        <li><a href="redirection.php">Votre compte</a></li>
                    </ul>
                    <div class="header-top-right">
                        <a href="mailto:agorafrancia@gmail.com">Contact</a>
                    </div>
                </div>
            </nav>
        </header>

        <main>
            <div class="categories-page-container">
                <div class="page-header">
                    <div class="container">
                        <h1>📂Nos Catégories</h1>
                        <p>Explorez notre vaste sélection d'articles organisés par catégories. Trouvez exactement ce que vous cherchez parmi nos milliers d'objets uniques et authentiques.</p>
                    </div>
                </div>
                <div class="search-categories">
                    <h2>🔍 Rechercher une catégorie</h2>
                    <div class="categories-search-bar">
                        <input type="text" class="search-input" id="categorySearch" placeholder="Tapez le nom d'une catégorie..." oninput="searchCategories()">
                        <button class="search-btn" onclick="searchCategories()"> Rechercher </button>
                    </div>
                    <div class="filter-tags">
                        <span class="filter-tag active" onclick="filterByType('all')">Toutes</span>
                        <span class="filter-tag" onclick="filterByType('trending')">🔥 Tendances</span>
                        <span class="filter-tag" onclick="filterByType('popular')">⭐ Populaires</span>
                        <span class="filter-tag" onclick="filterByType('new')">🆕 Nouvelles</span>
                        <span class="filter-tag" onclick="filterByType('premium')">💎 Premium</span>
                    </div>
                </div>
                <div class="categories-stats">
                    <div class="stat-card">
                        <span class="stat-number">25</span>
                        <span class="stat-label">Catégories disponibles</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number" id="totalArticles"><?php echo $totalItems; ?></span>
                        <span class="stat-label">Articles en vente</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number" id="totalVendeurs"><?php echo $totalVendeurs; ?></span>
                        <span class="stat-label">Vendeurs actifs</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">98%</span>
                        <span class="stat-label">Satisfaction client</span>
                    </div>
                </div>
                
                <div class="trending-section">
                    <h2 class="trending-title"> 🔥 Catégories en tendance</h2>
                    <div class="trending-categories">
                        <div class="trending-item" onclick="goToCategory('electronics')">
                            <span class="trending-icon">📱</span>
                            <div class="trending-name">Électronique</div>
                            <div class="trending-growth">+25% cette semaine</div>
                        </div>
                        <div class="trending-item" onclick="goToCategory('fashion')">
                            <span class="trending-icon">👕</span>
                            <div class="trending-name">Mode</div>
                            <div class="trending-growth">+18% cette semaine</div>
                        </div>
                        <div class="trending-item" onclick="goToCategory('antiques')">
                            <span class="trending-icon">🏺</span>
                            <div class="trending-name">Antiquités</div>
                            <div class="trending-growth">+32% cette semaine</div>
                        </div>
                        <div class="trending-item" onclick="goToCategory('art')">
                            <span class="trending-icon">🎨</span>
                            <div class="trending-name">Art</div>
                            <div class="trending-growth">+15% cette semaine</div>
                        </div>
                    </div>
                </div>
                
                <div class="categories-grid" id="categoriesGrid">
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; 2025 ING2 TD3 GP1. Tous droits réservés.</p>
            <span style="font-size: 20px;">-</span>
            <p style="display: inline-block; margin-left: 10px;">Contactez-nous :<a href="mailto:agorafrancia@gmail.com" style="text-decoration: none; color: inherit;">agorafrancia@gmail.com</a>
            </p>
            <span style="font-size: 20px;">-</span>
            <p style="display: inline-block; margin-left: 10px;">
                <a href="mentions.html" class="legal-link">Mentions légales</a>
            </p>
            <span style="font-size: 20px;">-</span>
            <p style="display: inline-block; margin-left: 10px;">
                <a href="conf.html" class="legal-link">Politique de Confidentialité</a>
            </p>
        </footer>
        
        <script>
            const dbStats = {
                totalItems: <?php echo $totalItems; ?>,
                totalVendeurs: <?php echo $totalVendeurs; ?>,
                totalAcheteurs: <?php echo $totalAcheteurs; ?>
            };

            const categoriesCounts = <?php echo json_encode($categoriesCounts, JSON_UNESCAPED_UNICODE); ?>;
            const popularItemsByCategory = <?php echo json_encode($popularItemsByCategory, JSON_UNESCAPED_UNICODE); ?>;

            console.log('Statistiques depuis la BDD:', dbStats);
            console.log('Compteurs par catégorie:', categoriesCounts);
            console.log('Articles populaires par catégorie:', popularItemsByCategory);

            const categories = [
                {
                    id: 'fashion',
                    name: 'Mode & Accessoires',
                    icon: '👕',
                    description: 'Vêtements, chaussures, sacs, bijoux et accessoires pour homme, femme et enfant.',
                    count: categoriesCounts['Mode & Accessoires'] || 0,
                    trend: '+22%',
                    type: ['trending', 'popular'],
                    popularItems: popularItemsByCategory['Mode & Accessoires'] || ['Nike', 'Adidas', 'Zara', 'H&M', 'Louis Vuitton'],
                    subtitle: 'Style et élégance'
                },
                {
                    id: 'sports',
                    name: 'Sports & Loisirs',
                    icon: '⚽',
                    description: 'Équipements sportifs, fitness, camping, pêche et matériel de loisirs.',
                    count: categoriesCounts['Sports & Loisirs'] || 0,
                    trend: '+18%',
                    type: ['trending'],
                    popularItems: popularItemsByCategory['Sports & Loisirs'] || ['Football', 'Vélo', 'Ski', 'Tennis', 'Fitness'],
                    subtitle: 'Bougez et amusez-vous'
                },
                {
                    id: 'books',
                    name: 'Livres & Multimédia',
                    icon: '📚',
                    description: 'Livres, BD, films, musique, jeux vidéo et contenus numériques.',
                    count: categoriesCounts['Livres & Multimédia'] || 0,
                    trend: '+7%',
                    type: ['popular'],
                    popularItems: popularItemsByCategory['Livres & Multimédia'] || ['Harry Potter', 'Manga', 'Histoire', 'Romans', 'Sciences'],
                    subtitle: 'Culture et savoir'
                },
                {
                    id: 'antiques',
                    name: 'Antiquités & Collection',
                    icon: '🏺',
                    description: 'Objets anciens, vintage, pièces de collection et articles patrimoniaux.',
                    count: categoriesCounts['Antiquités & Collection'] || 0,
                    trend: '+28%',
                    type: ['trending', 'premium'],
                    popularItems: popularItemsByCategory['Antiquités & Collection'] || ['Louis XVI', 'Art Déco', 'Vintage', 'Porcelaine', 'Horlogerie'],
                    subtitle: 'Trésors du passé'
                },
                {
                    id: 'art',
                    name: 'Art & Artisanat',
                    icon: '🎨',
                    description: 'Œuvres d\'art, peintures, sculptures, artisanat local et créations uniques.',
                    count: categoriesCounts['Art & Artisanat'] || 0,
                    trend: '+19%',
                    type: ['trending', 'premium'],
                    popularItems: popularItemsByCategory['Art & Artisanat'] || ['Peinture', 'Sculpture', 'Céramique', 'Photographie', 'Artisanat'],
                    subtitle: 'Créativité et talent'
                },
                {
                    id: 'jewelry',
                    name: 'Bijoux & Montres',
                    icon: '💎',
                    description: 'Bijoux précieux, montres de luxe, accessoires et pierres précieuses.',
                    count: categoriesCounts['Bijoux & Montres'] || 0,
                    trend: '+14%',
                    type: ['premium'],
                    popularItems: popularItemsByCategory['Bijoux & Montres'] || ['Rolex', 'Cartier', 'Or', 'Diamant', 'Argent'],
                    subtitle: 'Luxe et raffinement'
                },
            ];
            
            let filteredCategories = [...categories];
            let currentFilter = 'all';
            
            //affiche les catégories
            function displayCategories(categoriesToShow = filteredCategories) {
                const grid = document.getElementById('categoriesGrid');
                
                if (categoriesToShow.length === 0) {
                    grid.innerHTML = `
                        <div class="no-results">
                            <div class="no-results-icon">🔍</div>
                            <h3>Aucune catégorie trouvée</h3>
                            <p>Aucune catégorie ne correspond à vos critères de recherche. Essayez avec d'autres mots-clés ou réinitialisez les filtres.</p>
                            <button class="reset-filters-btn" onclick="resetFilters()">
                                Réinitialiser les filtres
                            </button>
                        </div>
                    `;
                    return;
                }
                
                grid.innerHTML = categoriesToShow.map(category => `
                    <div class="category-card" data-category="${category.id}" onclick="goToCategory('${category.id}')">
                        <div class="category-header">
                            <span class="category-icon">${category.icon}</span>
                            <h3 class="category-title">${category.name}</h3>
                            <p class="category-subtitle">${category.subtitle}</p>
                        </div>
                        <div class="category-content">
                            <p class="category-description">${category.description}</p>
                            <div class="category-stats">
                                <div class="category-count">${category.count.toLocaleString()} articles</div>
                                <div class="category-trend">📈 ${category.trend} cette semaine</div>
                            </div>
                            <div class="popular-items">
                                <div class="popular-items-title">Articles populaires :</div>
                                <div class="popular-items-list">
                                    ${category.popularItems.slice(0, 4).map(item =>
                                        `<span class="popular-tag">${item}</span>`
                                    ).join('')}
                                    ${category.popularItems.length > 4 ? `<span class="popular-tag">+${category.popularItems.length - 4}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
            
            //recherche catégorie
            function searchCategories() {
                const searchTerm = document.getElementById('categorySearch').value.toLowerCase().trim();
                
                if (!searchTerm) {
                    filteredCategories = currentFilter === 'all' ?
                        [...categories] :
                        categories.filter(cat => cat.type.includes(currentFilter));
                } else {
                    const baseCategories = currentFilter === 'all' ?
                        [...categories] :
                        categories.filter(cat => cat.type.includes(currentFilter));
                    
                    filteredCategories = baseCategories.filter(category =>
                        category.name.toLowerCase().includes(searchTerm) ||
                        category.description.toLowerCase().includes(searchTerm) ||
                        category.popularItems.some(item => item.toLowerCase().includes(searchTerm))
                    );
                }
                
                displayCategories(filteredCategories);
            }
            
            //filtre
            function filterByType(type) {
                currentFilter = type;
                
                document.querySelectorAll('.filter-tag').forEach(tag => {
                    tag.classList.remove('active');
                });
                event.target.classList.add('active');
                
                if (type === 'all') {
                    filteredCategories = [...categories];
                } else {
                    filteredCategories = categories.filter(category =>
                        category.type.includes(type)
                    );
                }
                
                const searchTerm = document.getElementById('categorySearch').value.toLowerCase().trim();
                if (searchTerm) {
                    filteredCategories = filteredCategories.filter(category =>
                        category.name.toLowerCase().includes(searchTerm) ||
                        category.description.toLowerCase().includes(searchTerm) ||
                        category.popularItems.some(item => item.toLowerCase().includes(searchTerm))
                    );
                }
                
                displayCategories(filteredCategories);
                showNotification(`Filtre appliqué: ${getFilterLabel(type)}`);
            }
            
            //label d'un filtre
            function getFilterLabel(type) {
                const labels = {
                    'all': 'Toutes les catégories',
                    'trending': 'Catégories tendances',
                    'popular': 'Catégories populaires',
                    'new': 'Nouvelles catégories',
                    'premium': 'Catégories premium'
                };
                return labels[type] || type;
            }
            
            //réinitialise
            function resetFilters() {
                currentFilter = 'all';
                document.getElementById('categorySearch').value = '';
                document.querySelectorAll('.filter-tag').forEach(tag => {
                    tag.classList.remove('active');
                });
                document.querySelector('.filter-tag[onclick="filterByType(\'all\')"]').classList.add('active');
                filteredCategories = [...categories];
                displayCategories(filteredCategories);
                showNotification('Filtres réinitialisés');
            }
            
            //aller vers une catégorie 
            function goToCategory(categoryId) {
                const category = categories.find(cat => cat.id === categoryId);
                if (category) {
                    showNotification(`Redirection vers ${category.name}...`);
                    setTimeout(() => {
                        const words = category.name.toLowerCase().split(/[\s&]+/); 
                        const initials = words.slice(0, 2).map(w => w[0]).join('');
                        window.location.href = `categories-${initials}.php`;
                    }, 1000);
                }
            }
            
            // créer une alerte 
            function createAlert(categoryId) {
                const category = categories.find(cat => cat.id === categoryId);
                if (category) {
                    if (confirm(`Créer une alerte pour la catégorie "${category.name}" ?\n\nVous serez notifié dès qu'un nouvel article correspondant sera disponible.`)) {
                        showNotification(`Alerte créée pour ${category.name} ! 🔔`);
                        setTimeout(() => {
                            window.location.href = `notifications.php?category=${categoryId}`;
                        }, 2000);
                    }
                }
            }
            
            //afficher des notifs
            function showNotification(message, type = 'success') {
                //supp  anciennes notifs
                const existingNotification = document.querySelector('.notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
                
                const notification = document.createElement('div');
                notification.className = `notification ${type === 'error' ? 'error' : ''}`;
                notification.textContent = message;
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'error' ? '#dc3545' : '#28a745'};
                    color: white;
                    padding: 15px 25px;
                    border-radius: 8px;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                    z-index: 10000;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                    max-width: 300px;
                    font-size: 14px;
                    line-height: 1.4;
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);
                
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (document.body.contains(notification)) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
            
            //met à jour les stats avec les vraies données de la BDD
            function updateStats() {
                const totalArticles = dbStats.totalItems;
                const totalCategories = categories.length;
                const activeVendors = dbStats.totalVendeurs;
                const satisfaction = 98;
                
                animateNumber('.stat-card:nth-child(1) .stat-number', totalCategories);
                animateNumber('#totalArticles', totalArticles);
                animateNumber('#totalVendeurs', activeVendors);
                animateNumber('.stat-card:nth-child(4) .stat-number', satisfaction, '%');
            }
            
            function animateNumber(selector, target, suffix = '') {
                const element = document.querySelector(selector);
                if (!element) return;
                
                const duration = 2000;
                const start = 0;
                const startTime = performance.now();
                
                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const current = Math.floor(start + (target - start) * progress);
                    
                    element.textContent = current.toLocaleString() + suffix;
                    
                    if (progress < 1) {
                        requestAnimationFrame(update);
                    }
                }
                
                requestAnimationFrame(update);
            }
            
            function handleUrlParams() {
                const urlParams = new URLSearchParams(window.location.search);
                const categoryParam = urlParams.get('category');
                const searchParam = urlParams.get('search');
                
                if (searchParam) {
                    document.getElementById('categorySearch').value = searchParam;
                    searchCategories();
                }
                
                if (categoryParam) {
                    setTimeout(() => {
                        const categoryCard = document.querySelector(`[data-category="${categoryParam}"]`);
                        if (categoryCard) {
                            categoryCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            categoryCard.style.border = '3px solid #ff6b6b';
                            categoryCard.style.boxShadow = '0 0 20px rgba(255, 107, 107, 0.5)';
                            setTimeout(() => {
                                categoryCard.style.border = '2px solid transparent';
                                categoryCard.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
                            }, 3000);
                        }
                    }, 500);
                }
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Page Catégories - Agora Francia chargée');
                
                displayCategories();
                updateStats();
                handleUrlParams();
                
                let searchTimeout;
                document.getElementById('categorySearch').addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(searchCategories, 300);
                });
                
                const sections = document.querySelectorAll('.page-header, .search-categories, .categories-stats, .trending-section');
                sections.forEach((section, index) => {
                    section.style.opacity = '0';
                    section.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        section.style.transition = 'all 0.6s ease';
                        section.style.opacity = '1';
                        section.style.transform = 'translateY(0)';
                    }, index * 200);
                });
                
                setTimeout(() => {
                    showNotification(`${dbStats.totalItems} articles disponibles ! 📂`);
                }, 1500);
            });
            
            function sortCategories(criteria) {
                switch(criteria) {
                    case 'name':
                        filteredCategories.sort((a, b) => a.name.localeCompare(b.name));
                        break;
                    case 'count':
                        filteredCategories.sort((a, b) => b.count - a.count);
                        break;
                    case 'trend':
                        filteredCategories.sort((a, b) => {
                            const trendA = parseInt(a.trend.replace(/[+%]/g, ''));
                            const trendB = parseInt(b.trend.replace(/[+%]/g, ''));
                            return trendB - trendA;
                        });
                        break;
                    default:
                        filteredCategories.sort((a, b) => b.count - a.count);
                }
                
                displayCategories(filteredCategories);
            }
            
            function getRecommendedCategories(userPreferences = []) {
                if (userPreferences.length === 0) {
                    return categories.filter(cat => cat.type.includes('popular')).slice(0, 6);
                }
                
                return categories.filter(cat =>
                    userPreferences.some(pref =>
                        cat.popularItems.some(item =>
                            item.toLowerCase().includes(pref.toLowerCase())
                        )
                    )
                );
            }
            
            function exportCategories() {
                const data = categories.map(cat => ({
                    nom: cat.name,
                    articles: cat.count,
                    tendance: cat.trend,
                    type: cat.type.join(', ')
                }));
                
                console.table(data);
                console.log('Statistiques BDD:', dbStats);
                showNotification('Données exportées dans la console (F12)');
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('.page-header h1').addEventListener('dblclick', function() {
                    if (confirm('Exporter les données dans la console ?')) {
                        exportCategories();
                    }
                });
            });
        </script>
    </body>
</html>