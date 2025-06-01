<?php
session_start();

//récup infos du vendeur :l'ID, photo et fond
if (isset($_SESSION['username'])) {
  try {
      $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia", "root", "root");
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      //récup ID, photo et fond de l'utilisateur
      $stmt = $pdo->prepare("SELECT ID_vendeurs, photo, fond FROM vendeurs WHERE NomUtilisateur_vendeurs = ?");
      $stmt->execute([$_SESSION['username']]);
      $vendeur = $stmt->fetch(PDO::FETCH_ASSOC);
      //afficher les infos récupérées s'il un y a un probleme
      echo "<!-- DEBUG : Username = " . $_SESSION['username'] . " -->";
      echo "<!-- DEBUG : Vendeur trouvé = " . ($vendeur ? 'OUI' : 'NON') . " -->";
      if ($vendeur) {
          echo "<!-- DEBUG : ID_vendeurs = " . $vendeur['ID_vendeurs'] . " -->";
          echo "<!-- DEBUG : photo = '" . $vendeur['photo'] . "' -->";
          echo "<!-- DEBUG : fond = '" . $vendeur['fond'] . "' -->";
          echo "<!-- DEBUG : photo vide = " . (empty($vendeur['photo']) ? 'OUI' : 'NON') . " -->";
          echo "<!-- DEBUG : fond vide = " . (empty($vendeur['fond']) ? 'OUI' : 'NON') . " -->";
      }
      //gestion pp
      if ($vendeur && !empty($vendeur['photo'])) {
          $photo_filename = $vendeur['photo'];
      } else {
          $photo_filename = 'profile.jpg';
      }
      //gestion image fond
      if ($vendeur && !empty($vendeur['fond'])) {
          $background_filename = $vendeur['fond'];
      } else {
          $background_filename = ''; 
      }
  } catch (PDOException $e) {
      //si erreur de base de donnée, utiliser les valeurs par défaut
      $photo_filename = 'profile.jpg';
      $background_filename = '';
  }
} else {
  $photo_filename = 'profile.jpg'; 
  $background_filename = ''; 
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mon Compte - Agora Francia</title>
  <link rel="stylesheet" href="styles.css" />
  <script src="script.js" defer></script>
  <style>
    <?php if (!empty($background_filename)): ?>
    /* Background personnalisé depuis base de données */
    body {
      background-image: url('uploads/backgrounds/<?php echo htmlspecialchars($background_filename); ?>');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
    }
    
    /* Overlay pour améliorer lisibilité */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.1);
      z-index: -1;
    }
    
    /* Améliore lisibilité du contenu */
    .header, .container {
      background-color: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(5px);
    }
    
    .container {
      border-radius: 10px;
      padding: 30px;
      margin: 20px auto;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    <?php endif; ?>
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
  <main class="container about-section">
    <h2>Bienvenue, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Utilisateur'; ?></h2>
    <!-- pp dynamique depuis la base de données -->
    <img src="uploads/profiles/<?php echo htmlspecialchars($photo_filename); ?>" 
         alt="Photo de profil" 
         style="width: 120px; height: 120px; border-radius: 50%; margin: 20px auto; display: block; object-fit: cover;" 
         onerror="this.src='profile.jpg'" />
    <div style="display: flex; flex-direction: column; align-items: center; gap: 20px; margin-top: 30px;">
      <a href="modifier_compte.php" class="auth-btn primary">Personnaliser le compte</a>
      <a href="ajouter_article.php" class="auth-btn">Publier un article</a>
      <a href="gerer_articles.php" class="auth-btn">Gérer ses articles</a>
    </div>
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