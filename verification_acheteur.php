<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ma commande - AGORA FRANCIA</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="icon" href="logo.ico" type="image/png" />
  <script src="script.js" defer></script>
  <style>
    .form-group {
      border: 1px solid #ccc;
      padding: 15px;
      border-radius: 8px;
      background-color: #fefefe;
      margin-bottom: 15px;
      transition: border-color 0.3s ease;
    }
    
    .form-group:focus-within {
      border-color: #397194;
      box-shadow: 0 0 0 2px rgba(57, 113, 148, 0.1);
    }
    
    .checkout-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }
    
    .checkout-form {
      display: flex;
      gap: 40px;
      flex-wrap: wrap;
      justify-content: center;
      margin-top: 30px;
    }
    
    .form-section {
      flex: 1;
      min-width: 320px;
      display: flex;
      flex-direction: column;
    }
    
    .section-title {
      font-size: 1.2em;
      font-weight: 600;
      color: #397194;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #397194;
    }
    
    .page-title {
      text-align: center;
      color: #333;
      font-size: 2em;
      margin-bottom: 10px;
    }
    
    .page-subtitle {
      text-align: center;
      color: #666;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
<header class="header">
    <div class="header-top">
      <div class="header-top-content">
        <div class="header-top-left">🏠 Toute la France</div>
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
              <li><a href="accueil.php">🏠 Accueil</a></li>
              <li><a href="toutparcourir.php">🔍 Tout parcourir</a></li>
              <li><a href="notification.php">🔔 Notifications</a></li>
              <li><a href="panier.php">🛒 Panier</a></li>
              <li><a href="redirection.php">👤 Votre compte</a></li>
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
              👤 <?= htmlspecialchars($_SESSION['username']); ?>
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
      <div class="checkout-container">
        <h2 class="page-title">🛒 Finaliser ma commande</h2>
        <p class="page-subtitle">Veuillez remplir vos informations pour finaliser votre achat</p>
        <form class="checkout-form" method="POST" action="gestfact.php">
          <div class="form-section">
            <h3 class="section-title">📋 Informations de livraison</h3>
            <div class="form-group">
              <input type="text" name="nom" placeholder="Nom *" required class="search-input">
            </div>
            <div class="form-group">
              <input type="text" name="prenom" placeholder="Prénom *" required class="search-input">
            </div>
            <div class="form-group">
              <input type="email" name="email" placeholder="Adresse e-mail *" required class="search-input">
            </div>
            <div class="form-group">
              <input type="text" name="adresse1" placeholder="Adresse ligne 1 *" required class="search-input">
            </div>
            <div class="form-group">
              <input type="text" name="adresse2" placeholder="Adresse ligne 2 (optionnel)" class="search-input">
            </div>
            <div class="form-group">
              <input type="text" name="ville" placeholder="Ville *" required class="search-input">
            </div>
            <div class="form-group">
              <input type="text" name="code_postal" placeholder="Code postal *" required class="search-input" pattern="[0-9]{5}">
            </div>
            <div class="form-group">
              <input type="text" name="pays" placeholder="Pays *" required class="search-input" value="France">
            </div>
            <div class="form-group">
              <input type="tel" name="telephone" placeholder="Numéro de téléphone *" required class="search-input">
            </div>
          </div>

          <div class="form-section">
            <h3 class="section-title">💳 Informations de paiement</h3>
            <div class="form-group">
              <label for="typeCarte" style="display: block; margin-bottom: 8px; font-weight: 500;">Type de carte *</label>
              <select id="typeCarte" name="type_carte" required class="search-input">
                <option value="">-- Sélectionner votre carte --</option>
                <option value="visa">💳 Visa</option>
                <option value="mastercard">💳 MasterCard</option>
                <option value="amex">💳 American Express</option>
              </select>
            </div>

            <div class="form-group">
              <label style="display: flex; align-items: center; font-weight: 500; cursor: pointer;">
                <input type="checkbox" name="utiliser_moyen_sauvegarde" style="margin-right: 10px; transform: scale(1.2);">
                Utiliser le moyen de paiement sauvegardé
              </label>
            </div>

            <div class="form-group">
              <input type="text" name="numero_carte" placeholder="Numéro de carte *" required class="search-input" maxlength="19" pattern="[0-9\s]{13,19}">
            </div>
            
            <div class="form-group">
              <input type="text" name="nom_carte" placeholder="Nom sur la carte *" required class="search-input">
            </div>
            
            <div style="display: flex; gap: 15px;">
              <div class="form-group" style="flex: 1;">
                <input type="text" name="date_expiration" placeholder="MM/AA *" required class="search-input" maxlength="5" pattern="[0-9]{2}/[0-9]{2}">
              </div>
              
              <div class="form-group" style="flex: 1;">
                <input type="text" name="code_securite" placeholder="CVV *" required class="search-input" maxlength="4" pattern="[0-9]{3,4}">
              </div>
            </div>

            <div class="form-group">
              <label style="display: flex; align-items: center; font-weight: 500; cursor: pointer;">
                <input type="checkbox" name="sauvegarder_carte" style="margin-right: 10px; transform: scale(1.2);">
                Sauvegarder cette carte pour mes prochains achats
              </label>
            </div>

            <div class="form-group">
              <label style="display: flex; align-items: center; font-weight: 500; cursor: pointer;">
                <input type="checkbox" name="accepter_conditions" required style="margin-right: 10px; transform: scale(1.2);">
                J'accepte les <a href="mentions.html" style="color: #397194; text-decoration: underline;">conditions générales de vente</a>
              </label>
            </div>

            <div class="form-group" style="margin-top: 30px;">
              <button type="submit" class="auth-btn primary" style="width: 100%; padding: 15px; font-size: 1.1em; font-weight: 600;">
                🛍️ Valider ma commande
              </button>
            </div>
            
            <div style="text-align: center; margin-top: 15px; color: #666; font-size: 0.9em;">
              <p>🔒 Paiement 100% sécurisé - SSL</p>
              <p>💳 Nous acceptons Visa, MasterCard et American Express</p>
            </div>
          </div>
        </form>
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