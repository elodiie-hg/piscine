<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ajouter un article</title>
  <link rel="stylesheet" href="styles.css" />
  <script src="script.js" defer></script>
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
  <main class="container about-section">
    <br>
    <h2>Ajouter un article</h2>
    <?php if (isset($_GET['error'])): ?>
  <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
    ⚠️ <?php echo htmlspecialchars($_GET['error']); ?>
  </div>
<?php endif; ?>

    <form action="traitement_ajout.php" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px; max-width: 600px; margin: auto;">
      <input type="text" name="nom" placeholder="Nom du produit" required class="search-input">
      <textarea name="description" placeholder="Description" required class="search-input" rows="4"></textarea>
      <input type="number" name="prix" placeholder="Prix" required class="search-input">
    
      <label for="categorie">Catégorie</label>
      <select id="categorie" name="categorie" required class="search-input">
        <option value="">-- Choisir une catégorie --</option>
        <option value="Mode & Accessoires">Mode & Accessoires</option>
        <option value="Sports & Loisirs">Sports & Loisirs</option>
        <option value="Livres & Multimédia">Livres & Multimédia</option>
        <option value="Antiquités & Collection">Antiquités & Collection</option>
        <option value="Art & Artisanat">Art & Artisanat</option>
        <option value="Bijoux & Montres">Bijoux & Montres</option>
      </select>
    
      <label for="type">Type de vente</label>
      <select id="type" name="type" required class="search-input">
        <option value="">-- Choisir un type de vente --</option>
        <option value="immediate">Immédiate</option>
        <option value="negociation">Négociation</option>
        <option value="offre">Meilleure offre</option>
      </select>
    
      <label for="photo">Photo</label>
      <input type="file" name="photo" id="photo" accept="image/*" required class="search-input">
    
      <label for="video">Vidéo</label>
      <input type="file" name="video" id="video" accept="video/*" class="search-input">
    
      <button type="submit" class="auth-btn primary">Publier l'article</button>
    </form>
    
  </main>

  <footer>
    <p>&copy; 2025 ING2 TD3 GP1. Tous droits réservés.</p>
    <span>-</span>
    <p><a href="mailto:agorafrancia@gmail.com">Contactez-nous</a></p>
    <span>-</span>
    <p><a href="mentions.html" class="legal-link">Mentions légales</a></p>
    <span>-</span>
    <p><a href="conf.html" class="legal-link">Politique de Confidentialité</a></p>
  </footer>
</body>
</html>
