<?php
session_start();

// Traitement  formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //changer nom d'utilisateur
        if (isset($_POST['change_name']) && !empty($_POST['new_username'])) {
            $new_username = trim($_POST['new_username']);
            //vérifier que le nom n'est pas déjà pris
            $check_stmt = $pdo->prepare("SELECT ID_vendeurs FROM vendeurs WHERE NomUtilisateur_vendeurs = ? AND NomUtilisateur_vendeurs != ?");
            $check_stmt->execute([$new_username, $_SESSION['username']]);
            
            if ($check_stmt->fetch()) {
                $error_message = "Ce nom d'utilisateur est déjà pris.";
            } else {
                $stmt = $pdo->prepare("UPDATE vendeurs SET NomUtilisateur_vendeurs = ? WHERE NomUtilisateur_vendeurs = ?");
                $stmt->execute([$new_username, $_SESSION['username']]);
                $_SESSION['username'] = $new_username; 
                $success_message = "Nom d'utilisateur modifié avec succès !";
            }
        }
        //changer photo
        if (isset($_POST['change_photo']) && !empty($_POST['new_photo'])) {
            $new_photo = trim($_POST['new_photo']);
            $stmt = $pdo->prepare("UPDATE vendeurs SET photo = ? WHERE NomUtilisateur_vendeurs = ?");
            $stmt->execute([$new_photo, $_SESSION['username']]);
            $success_message = "Photo modifiée avec succès !";
        }
        //changer arrière-plan
        if (isset($_POST['change_background']) && !empty($_POST['new_background'])) {
            $new_background = trim($_POST['new_background']);
            $stmt = $pdo->prepare("UPDATE vendeurs SET fond = ? WHERE NomUtilisateur_vendeurs = ?");
            $stmt->execute([$new_background, $_SESSION['username']]);
            $success_message = "Arrière-plan modifié avec succès !";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur de base de données : " . $e->getMessage();
    }
}

//récup les infos actuelles
$current_username = $_SESSION['username'] ?? '';
$current_photo = '';
$current_background = '';

if (isset($_SESSION['username'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->prepare("SELECT photo, fond FROM vendeurs WHERE NomUtilisateur_vendeurs = ?");
        $stmt->execute([$_SESSION['username']]);
        $vendeur = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($vendeur) {
            $current_photo = $vendeur['photo'] ?? '';
            $current_background = $vendeur['fond'] ?? '';
        }
    } catch (PDOException $e) {
        // Erreur silencieuse
    }
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
    /* Styles pour les modals */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    
    .modal-content {
      background-color: white;
      margin: 15% auto;
      padding: 30px;
      border-radius: 10px;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 1px solid #eee;
      padding-bottom: 15px;
    }
    
    .modal-title {
      font-size: 1.5em;
      color: #397194;
      margin: 0;
    }
    
    .close {
      color: #aaa;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      line-height: 1;
    }
    
    .close:hover {
      color: #000;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #333;
    }
    
    .form-group input {
      width: 100%;
      padding: 12px;
      border: 2px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      box-sizing: border-box;
    }
    
    .form-group input:focus {
      border-color: #397194;
      outline: none;
    }
    
    .current-value {
      background-color: #f8f9fa;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      color: #666;
      font-style: italic;
    }
    
    .modal-buttons {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 20px;
    }
    
    .btn-cancel {
      background-color: #6c757d;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    
    .btn-cancel:hover {
      background-color: #5a6268;
    }
    
    .alert {
      padding: 15px;
      margin: 20px 0;
      border-radius: 5px;
      font-weight: 500;
    }
    
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <header class="header">
    <!-- Barre sup -->
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
    <!-- nav -->
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
    <h2>Personnaliser le compte de <?php echo htmlspecialchars($current_username); ?></h2>
    
    <!-- Messages de succès/erreur -->
    <?php if (isset($success_message)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    
    <div class="card" style="display:flex; flex-direction:column; gap:15px; align-items:center;">
      <button class="auth-btn" onclick="openModal('nameModal')">Changer le nom</button>
      <button class="auth-btn" onclick="openModal('photoModal')">Changer la photo</button>
      <button class="auth-btn" onclick="openModal('backgroundModal')">Changer l'arrière plan</button>
    </div>
  </main>
  <!-- changer le nom -->
  <div id="nameModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Changer le nom d'utilisateur</h3>
        <span class="close" onclick="closeModal('nameModal')">&times;</span>
      </div>
      
      <div class="current-value">
        Nom actuel : <?php echo htmlspecialchars($current_username); ?>
      </div>
      
      <form method="POST">
        <div class="form-group">
          <label for="new_username">Nouveau nom d'utilisateur :</label>
          <input type="text" id="new_username" name="new_username" required>
        </div>
        
        <div class="modal-buttons">
          <button type="button" class="btn-cancel" onclick="closeModal('nameModal')">Annuler</button>
          <button type="submit" name="change_name" class="auth-btn primary">Confirmer</button>
        </div>
      </form>
    </div>
  </div>
  <!--  changer la photo -->
  <div id="photoModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Changer la photo de profil</h3>
        <span class="close" onclick="closeModal('photoModal')">&times;</span>
      </div>
      
      <div class="current-value">
        Photo actuelle : <?php echo htmlspecialchars($current_photo ?: 'Aucune photo définie'); ?>
      </div>
      
      <form method="POST">
        <div class="form-group">
          <label for="new_photo">Nom du fichier photo (ex: mon_avatar.jpg) :</label>
          <input type="text" id="new_photo" name="new_photo" required placeholder="ex: photo123.jpg">
          <small style="color: #666; display: block; margin-top: 5px;">
            Le fichier doit être présent dans le dossier uploads/profiles/
          </small>
        </div>
        
        <div class="modal-buttons">
          <button type="button" class="btn-cancel" onclick="closeModal('photoModal')">Annuler</button>
          <button type="submit" name="change_photo" class="auth-btn primary">Confirmer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- changer l'arrière-plan -->
  <div id="backgroundModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Changer l'arrière-plan</h3>
        <span class="close" onclick="closeModal('backgroundModal')">&times;</span>
      </div>
      
      <div class="current-value">
        Arrière-plan actuel : <?php echo htmlspecialchars($current_background ?: 'Aucun arrière-plan défini'); ?>
      </div>
      
      <form method="POST">
        <div class="form-group">
          <label for="new_background">Nom du fichier d'arrière-plan (ex: fond123.jpg) :</label>
          <input type="text" id="new_background" name="new_background" required placeholder="ex: fond123.jpg">
          <small style="color: #666; display: block; margin-top: 5px;">
            Le fichier doit être présent dans le dossier uploads/backgrounds/
          </small>
        </div>
        
        <div class="modal-buttons">
          <button type="button" class="btn-cancel" onclick="closeModal('backgroundModal')">Annuler</button>
          <button type="submit" name="change_background" class="auth-btn primary">Confirmer</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 ING2 TD3 GP1. Tous droits réservés.</p>
    <span>-</span>
    <p><a href="mailto:agorafrancia@gmail.com">agorafrancia@gmail.com</a></p>
    <span>-</span>
    <p><a href="mentions.html" class="legal-link">Mentions légales</a></p>
    <span>-</span>
    <p><a href="conf.html" class="legal-link">Politique de Confidentialité</a></p>
  </footer>

  <script>
    function openModal(modalId) {
      document.getElementById(modalId).style.display = 'block';
    }
    
    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }
    
    // Fermer le modal en cliquant en dehors
    window.onclick = function(event) {
      if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
      }
    }
    
    // Fermer les modals avec la touche Escape
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
          modal.style.display = 'none';
        });
      }
    });
  </script>
</body>
</html>