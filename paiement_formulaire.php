<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Informations de paiement</title>
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
    <h2>Acheteur : Mes informations personnelles</h2>
    <form style="display: flex; flex-direction: column; gap: 15px; max-width: 600px; margin: auto;" action="panier.php" method="GET">
      <input type="text" placeholder="Nom" required class="search-input">
      <input type="text" placeholder="Prénom" required class="search-input">
      <input type="email" placeholder="Email" required class="search-input">
      <input type="text" placeholder="Adresse" required class="search-input">

      <label for="carte">Type de carte</label>
      <select id="carte" required class="search-input">
        <option value="">-- Sélectionnez un type de carte --</option>
        <option value="visa">Visa</option>
        <option value="mastercard">MasterCard</option>
        <option value="amex">American Express</option>
      </select>

      <input type="text" placeholder="Numéro de carte" required class="search-input">
      <input type="text" placeholder="Nom sur la carte" required class="search-input">
      <input type="text" placeholder="Date d’expiration (MM/AA)" required class="search-input">
      <input type="text" placeholder="Code de sécurité" required class="search-input">

      <button type="submit" class="auth-btn primary">Valider</button>
    </form>
  </main>

  <footer>
    <p>&copy; 2025 ING2 TD3 GP1. Tous droits réservés.</p>
    <span>-</span>
    <p><a href="mailto:agorafrancia@gmail.com">agorafrancia@gmail.com</a></p>
    <span>-</span>
    <p><a href="mentions.html" class="legal-link">Mentions légales</a></p>
    <span>-</span>
    <p><a href="conf.html" class="legal-link">Politique de Confidentialité</a></p>
  </footer>
</body>
</html>
