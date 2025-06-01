<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia;charset=utf8mb4", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //récup ID, photo et fond si utilisateur est connecté
    if (isset($_SESSION['username'])) {
        $stmt = $pdo->prepare("SELECT ID_vendeurs, photo, fond FROM vendeurs WHERE NomUtilisateur_vendeurs = ?");
        $stmt->execute([$_SESSION['username']]);
        $vendeur = $stmt->fetch(PDO::FETCH_ASSOC);

        $photo_filename = !empty($vendeur['photo']) ? $vendeur['photo'] : 'profile.jpg';
        $background_filename = !empty($vendeur['fond']) ? $vendeur['fond'] : '';
    } else {
        $photo_filename = 'profile.jpg';
        $background_filename = '';
    }
    //requête pour produit "Bijoux & Montres"
    $sql = "SELECT * FROM items WHERE Catégorie_item = 'Bijoux & Montres'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Catégorie : Bijoux & Montres</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="icon" href="logo.ico" type="image/png" />
  <script src="script.js" defer></script>
  <style>
    <?php if (!empty($background_filename)): ?>
    body {
      background-image: url('uploads/backgrounds/<?php echo htmlspecialchars($background_filename); ?>');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      background-repeat: no-repeat;
    }
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
    .header, .items-container {
      background-color: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(5px);
    }
    <?php endif; ?>

    .items-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
        margin: 30px auto;
        padding: 20px;
        max-width: 1200px;
    }
    .item-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        padding: 15px;
        transition: transform 0.2s;
    }
    .item-card:hover {
        transform: translateY(-5px);
    }
    .item-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
    }
    .item-card h3 {
        margin: 10px 0 5px;
        font-size: 18px;
        color: #333;
    }
    .item-card p {
        margin: 5px 0;
        color: #666;
    }
    .item-card strong {
        color: #007b5e;
    }
    .item-card form button {
        margin-top:10px;
        padding: 8px 12px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
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

<h1 style="text-align:center; margin-top: 20px;">Objets - Bijoux & Montres</h1>

<div class="items-container">
    <?php if (count($items) > 0): ?>
        <?php foreach ($items as $item): ?>
            <div class="item-card">
                <img src="uploads/items/<?= htmlspecialchars($item['Photo_item']) ?>" alt="<?= htmlspecialchars($item['Nom_item']) ?>">
                <h3><?= htmlspecialchars($item['Nom_item']) ?></h3>
                <p><?= htmlspecialchars($item['Descriptions_item']) ?></p>
                <p><strong><?= number_format($item['Prix_item'], 2) ?> €</strong></p>

                <?php if (isset($_SESSION['username'])): ?>
                    <form action="ajouter_panier.php" method="post">
                        <input type="hidden" name="ID_item" value="<?= $item['ID_item'] ?>">
                        <button type="submit">🛒 Ajouter au panier</button>
                    </form>
                <?php else: ?>
                    <p style="color: #888; font-size: 14px;">Connectez-vous pour ajouter au panier</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center;">Aucun objet trouvé dans la catégorie Bijoux & Montres'.</p>
    <?php endif; ?>
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

</body>
</html>
