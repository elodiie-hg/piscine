<?php
session_start();

$host = 'localhost';
$dbname = 'agorafrancia';
$user = 'root';
$pass = 'root';

$flashSalesItems = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $pdo->query("
        SELECT ID_item, Nom_item, Photo_item, Prix_item, Descriptions_item, Catégorie_item 
        FROM items 
        WHERE Nom_item IS NOT NULL AND Prix_item IS NOT NULL 
        ORDER BY RAND() 
        LIMIT 6
    ");
    $flashSalesItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($flashSalesItems as &$item) {
        $discount = rand(10, 40);
        $originalPrice = floatval($item['Prix_item']);
        $discountedPrice = $originalPrice * (1 - $discount / 100);
        $item['original_price'] = $originalPrice;
        $item['discounted_price'] = round($discountedPrice, 2);
        $item['discount_percentage'] = $discount;
    }
    echo "<!-- DEBUG: Récupéré " . count($flashSalesItems) . " articles pour les ventes flash -->";
    echo "<!-- DEBUG: Articles: " . print_r(array_column($flashSalesItems, 'Nom_item'), true) . " -->";
} catch (PDOException $e) {
    echo "<!-- DEBUG: Erreur de connexion BDD: " . $e->getMessage() . " -->";
    $flashSalesItems = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AGORA FRANCIA</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="icon" href="logo.ico" type="image/png" />
  <script src="script.js" defer></script>
  <style>
    /*stye pour page*/
    .flash-sales-section {
      background: linear-gradient(135deg, #ff6b6b, #ffd93d);
      padding: 60px 0;
      margin: 40px 0;
      position: relative;
      overflow: hidden;
    }
    
    .flash-sales-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      opacity: 0.3;
    }
    
    .flash-sales-title {
      text-align: center;
      font-size: 36px;
      color: white;
      margin-bottom: 10px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
      position: relative;
      z-index: 2;
    }
    
    .flash-icon {
      font-size: 40px;
      animation: flash 1.5s infinite alternate;
    }
    
    @keyframes flash {
      0% { opacity: 1; transform: scale(1); }
      100% { opacity: 0.7; transform: scale(1.1); }
    }
    
    .flash-sales-subtitle {
      text-align: center;
      color: white;
      font-size: 18px;
      margin-bottom: 40px;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
      position: relative;
      z-index: 2;
    }
    
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 25px;
      margin-bottom: 40px;
      position: relative;
      z-index: 2;
    }
    
    .product-card {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      transition: all 0.3s ease;
      cursor: pointer;
      border: 2px solid transparent;
    }
    
    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
      border-color: #ff6b6b;
    }
    
    .product-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-image {
      transform: scale(1.05);
    }
    
    .product-info {
      padding: 20px;
    }
    
    .product-title {
      font-size: 18px;
      font-weight: bold;
      color: #333;
      margin-bottom: 8px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-description {
      color: #666;
      font-size: 14px;
      margin-bottom: 12px;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-category {
      display: inline-block;
      background: rgba(57, 113, 148, 0.1);
      color: #397194;
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 500;
      margin-bottom: 15px;
    }
    
    .product-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 15px;
    }
    
    .product-price {
      font-size: 24px;
      font-weight: bold;
      color: #ff6b6b;
    }
    
    .add-to-cart-btn {
      background: linear-gradient(135deg, #397194, #252d70);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 25px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 14px;
    }
    
    .add-to-cart-btn:hover {
      background: linear-gradient(135deg, #252d70, #397194);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(57, 113, 148, 0.3);
    }
    
    .view-all-btn {
      display: block;
      width: fit-content;
      margin: 0 auto;
      background: white;
      color: #ff6b6b;
      padding: 15px 30px;
      border-radius: 25px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: relative;
      z-index: 2;
    }
    
    .view-all-btn:hover {
      background: #ff6b6b;
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(255, 107, 107, 0.3);
    }
    
    .no-products {
      text-align: center;
      color: white;
      padding: 40px;
      position: relative;
      z-index: 2;
    }
    
    .no-products h3 {
      font-size: 24px;
      margin-bottom: 15px;
    }
    
    .no-products p {
      font-size: 16px;
      opacity: 0.9;
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
    <section class="about-section">
      <div class="container">
        <h3>Bienvenue sur Agora Francia</h3>
        <p>
          Inspiré du marché grec antique, <strong>Agora Francia</strong> est votre plateforme de commerce en ligne.
          Achetez, vendez ou négociez en toute simplicité :
        </p>
        <ul>
          <li><strong>Vente immédiate</strong> : achetez vos articles et recevez-les rapidement.</li>
          <li><strong>Vente par négociation</strong> : proposez un prix pour des articles d'occasion ou avec un petit défaut.</li>
          <li><strong>Vente par meilleure offre</strong> : remportez des objets uniques en proposant votre meilleure offre.</li>
        </ul>
        <p>
          Consultez notre <strong>Sélection du jour</strong> pour découvrir les dernières nouveautés ou les meilleures ventes de la semaine.
        </p>
      </div>
    </section>
    <section class="flash-sales-section">
      <div class="container">
        <h2 class="flash-sales-title">
          <span class="flash-icon">⚡</span>
          Ventes Flash
          <span class="flash-icon">⚡</span>
        </h2>
        <p class="flash-sales-subtitle">
          Découvrez nos offres flash avec des réductions exceptionnelles - Seulement 6 articles sélectionnés !
        </p>
        <div class="products-grid" id="bestSellersGrid">
        </div>
        <a href="toutparcourir.php" class="view-all-btn">
          Voir tous les articles ➜
        </a>
      </div>
    </section>
    <section class="about-section">
      <div class="container">
        <h3>Où nous trouver ?</h3>
        <div class="address-section">
          <p>📍 10 Rue Sextius Michel<br/>75015 Paris</p>
          <div style="margin-top: 20px;">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2625.2166741294083!2d2.2877382156733277!3d48.85102277928796!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6700d6cbab9cf%3A0x6a38cd9d8a7a7ff2!2s10%20Rue%20Sextius%20Michel%2C%2075015%20Paris%2C%20France!5e0!3m2!1sfr!2sfr!4v1652557490000!5m2!1sfr!2sfr"
              width="100%"
              height="350"
              style="border:0; border-radius: 10px;"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </div>
    </section>
  </main>
  <footer>
    <p>&copy; 2025 ING2 TD3 GP1. Tous droits réservés.</p>
    <span style="font-size: 20px;">-</span>
    <p style="display: inline-block; margin-left: 10px;">
      Contactez-nous :
      <a href="mailto:agorafrancia@gmail.com" style="text-decoration: none; color: inherit;">agorafrancia@gmail.com</a>
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
    const flashSalesData = <?php echo json_encode($flashSalesItems, JSON_UNESCAPED_UNICODE); ?>;
    console.log('Articles ventes flash depuis la BDD:', flashSalesData);
    function displayFlashSales() {
      const grid = document.getElementById('bestSellersGrid');
      if (!flashSalesData || flashSalesData.length === 0) {
        grid.innerHTML = `
          <div class="no-products">
            <h3>🎯 Bientôt disponible !</h3>
            <p>Nos ventes flash arrivent bientôt. Revenez dans quelques instants pour découvrir nos dernières nouveautés !</p>
          </div>
        `;
        return;
      }
      grid.innerHTML = flashSalesData.map(item => {
        let imagePath = 'https://via.placeholder.com/280x200/397194/FFFFFF?text=Pas+d%27image';
        if (item.Photo_item && item.Photo_item.trim() !== '') {
          if (item.Photo_item.startsWith('http')) {
            imagePath = item.Photo_item;
          } else {
            imagePath = `uploads/${item.Photo_item}`;
          }
        }
        const originalPrice = parseFloat(item.original_price);
        const discountedPrice = parseFloat(item.discounted_price);
        const discountPercentage = parseInt(item.discount_percentage);
        const formattedOriginalPrice = !isNaN(originalPrice) ? originalPrice.toLocaleString('fr-FR', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }) : 'Prix non disponible';
        const formattedDiscountedPrice = !isNaN(discountedPrice) ? discountedPrice.toLocaleString('fr-FR', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }) : 'Prix non disponible';
        return `
          <div class="product-card" onclick="viewProduct(${item.ID_item})">
            <div class="flash-badge">-${discountPercentage}% ⚡</div>
            <img 
              src="${imagePath}" 
              alt="${escapeHtml(item.Nom_item)}" 
              class="product-image"
              onerror="this.src='https://via.placeholder.com/280x200/397194/FFFFFF?text=Image+non+disponible'"
            />
            <div class="product-info">
              <h3 class="product-title">${escapeHtml(item.Nom_item)}</h3>
              ${item.Descriptions_item ? `<p class="product-description">${escapeHtml(item.Descriptions_item)}</p>` : ''}
              ${item.Catégorie_item ? `<span class="product-category">${escapeHtml(item.Catégorie_item)}</span>` : ''}
              <div class="product-footer">
                <div class="price-container">
                  <div class="product-price">${formattedDiscountedPrice} €</div>
                  <div class="original-price">${formattedOriginalPrice} €</div>
                </div>
                <div class="discount-badge">-${discountPercentage}%</div>
              </div>
              <button 
                class="add-to-cart-btn" 
                onclick="addToCart(${item.ID_item}, event)"
                title="Ajouter au panier"
                style="width: 100%; margin-top: 10px;"
              >
                🛒 Ajouter au panier
              </button>
            </div>
          </div>
        `;
      }).join('');
    }

    function escapeHtml(text) {
      if (!text) return '';
      const div = document.createElement('div');
      div.textContent = text;
      return div.innerHTML;
    }

    function viewProduct(itemId) {
      console.log(`Affichage du produit ID: ${itemId}`);
      window.location.href = `produit.php?id=${itemId}`;
    }

    function addToCart(itemId, event) {
      if (event) {
        event.stopPropagation();
      }
      
      console.log(`Ajout au panier: Article ID ${itemId}`);
      

      const item = flashSalesData.find(product => product.ID_item == itemId);
      
      if (item) {
        showNotification(`"${item.Nom_item}" ajouté au panier ! 🛒`);
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '✅ Ajouté !';
        button.style.background = '#28a745';
        
        setTimeout(() => {
          button.innerHTML = originalText;
          button.style.background = '';
        }, 2000);
      } else {
        showNotification('Erreur: Article non trouvé', 'error');
      }
    }

    function showNotification(message, type = 'success') {
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

    function addToCartAjax(itemId) {
      fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ item_id: itemId })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          console.log('Article ajouté au panier avec succès');
        } else {
          console.error('Erreur lors de l\'ajout au panier:', data.message);
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      console.log('Page d\'accueil Agora Francia chargée');
      
      displayFlashSales();
      
      setTimeout(() => {
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach((card, index) => {
          card.style.opacity = '0';
          card.style.transform = 'translateY(30px)';
          setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, index * 100);
        });
      }, 200);
      
      setTimeout(() => {
        if (flashSalesData && flashSalesData.length > 0) {
          showNotification(`${flashSalesData.length} nouveaux articles disponibles ! ⚡`);
        }
      }, 1500);
    });
  </script>
</body>

</html>