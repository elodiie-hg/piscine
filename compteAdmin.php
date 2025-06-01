<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Compte Admin - AGORA FRANCIA</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="icon" href="logo.ico" type="image/x-icon" />
  <script src="script.js" defer></script>
  <style>
    html, body {
      max-width: 100%;
      overflow-x: hidden;
      box-sizing: border-box;
    }
    *, *::before, *::after {
      box-sizing: inherit;
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
        <!-- Actions utilisateur -->
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
        <h3>Compte Administrateur</h3>
        <p>Bienvenue sur votre espace d'administration.</p>
        <div style="margin-top: 30px;">
          <button onclick="window.location.href='ajouter_article.php'" class="auth-btn primary">➕ Ajouter un article</button>
          <button onclick="window.location.href='gestut.php'" class="auth-btn primary">👥 Gestion utilisateur</button>
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
</body>
</html>
