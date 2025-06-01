<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: connexion.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia;charset=utf8mb4", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //ID de l'acheteur connecté
    $stmtUser = $pdo->prepare("SELECT ID_acheteurs FROM acheteurs WHERE NomUtilisateur_acheteurs = ?");
    $stmtUser->execute([$_SESSION['username']]);
    $acheteur = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$acheteur) {
        die("Utilisateur non reconnu.");
    }

    $idAcheteur = $acheteur['ID_acheteurs'];

    //article ajoutés au panier
    $stmt = $pdo->prepare("
        SELECT i.*, p.ID_panier
        FROM panier p
        INNER JOIN items i ON p.ID_item = i.ID_item
        WHERE p.ID_acheteurs = ?
    ");
    $stmt->execute([$idAcheteur]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .panier-container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        .article-card {
            display: flex;
            gap: 20px;
            background: #fff;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .article-card img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .article-details h3 { margin: 0 0 10px; }
        .article-details p { margin: 5px 0; color: #555; }
        .article-details strong { font-size: 18px; color: #007b5e; }
        .delete-btn {
            background-color: #c0392b;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #e74c3c;
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
<div class="panier-container">
    <h2 style="text-align: center;">🛒 Mon Panier</h2>

    <?php if (count($articles) > 0): ?>
        <?php foreach ($articles as $item): ?>
            <div class="article-card">
                <img src="uploads/items/<?= htmlspecialchars($item['Photo_item']) ?>" alt="<?= htmlspecialchars($item['Nom_item']) ?>">
                <div class="article-details">
                    <h3><?= htmlspecialchars($item['Nom_item']) ?></h3>
                    <p><?= htmlspecialchars($item['Descriptions_item']) ?></p>
                    <p><strong><?= number_format($item['Prix_item'], 2) ?> €</strong></p>

                    <form action="supprimer_du_panier.php" method="post" onsubmit="return confirm('Supprimer cet article ?');">
                        <input type="hidden" name="id_panier" value="<?= $item['ID_panier'] ?>">
                        <button type="submit" class="delete-btn">🗑 Supprimer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align: center;">Votre panier est vide.</p>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 30px;">
        <a href="verification_acheteur.php">
            <button style="padding: 10px 20px; font-size: 16px;">Procéder à la vérification</button>
        </a>
    </div>
</div>

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
