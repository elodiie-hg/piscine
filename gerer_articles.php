<?php
session_start();

//vérifier que utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: connexion.php?error=" . urlencode("Vous devez être connecté pour gérer vos articles."));
    exit();
}
//traitement de la suppression d'articles
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article'])) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $article_id = intval($_POST['article_id']);
        //vérifier que l'article appartient à l'utilisateur connecté
        $stmt = $pdo->prepare("
            SELECT i.ID_item, i.Photo_item, i.Vidéo_item 
            FROM items i 
            JOIN vendeurs v ON i.ID_vendeurs = v.ID_vendeurs 
            WHERE i.ID_item = ? AND v.NomUtilisateur_vendeurs = ?
        ");
        $stmt->execute([$article_id, $_SESSION['username']]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($article) {
            //supp les fichiers associés
            if (!empty($article['Photo_item']) && file_exists('uploads/items/' . $article['Photo_item'])) {
                unlink('uploads/items/' . $article['Photo_item']);
            }
            if (!empty($article['Vidéo_item']) && file_exists('uploads/videos/' . $article['Vidéo_item'])) {
                unlink('uploads/videos/' . $article['Vidéo_item']);
            }
            //supp l'article de bdd
            $stmt = $pdo->prepare("DELETE FROM items WHERE ID_item = ?");
            $stmt->execute([$article_id]);
            
            $success_message = "Article supprimé avec succès !";
        } else {
            $error_message = "Article introuvable ou vous n'avez pas les droits pour le supprimer.";
        }
    } catch (PDOException $e) {
        $error_message = "Erreur de base de données : " . $e->getMessage();
    }
}
//récup les articles de l'utilisateur connecté
$articles = [];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //récup tous les articles de l'utilisateur connecté
    $stmt = $pdo->prepare("
        SELECT i.ID_item, i.Nom_item, i.Prix_item, i.Catégorie_item, i.Type_item, i.Photo_item
        FROM items i 
        JOIN vendeurs v ON i.ID_vendeurs = v.ID_vendeurs 
        WHERE v.NomUtilisateur_vendeurs = ?
        ORDER BY i.ID_item DESC
    ");
    $stmt->execute([$_SESSION['username']]);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error_message = "Erreur lors de la récupération des articles : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8" />
 <meta name="viewport" content="width=device-width, initial-scale=1.0" />
 <title>Gérer mes articles</title>
 <link rel="stylesheet" href="styles.css" />
 <script src="script.js" defer></script>
 <style>
 .article-item {
     display: flex;
     justify-content: space-between;
     align-items: center;
     padding: 15px;
     border: 1px solid #ddd;
     border-radius: 5px;
     margin-bottom: 10px;
     background-color: #f9f9f9;
 }
 
 .article-info {
     display: flex;
     flex-direction: column;
     flex-grow: 1;
     margin-right: 15px;
 }
 
 .article-name {
     font-weight: bold;
     font-size: 1.1em;
     color: #333;
     margin-bottom: 5px;
 }
 
 .article-details {
     color: #666;
     font-size: 0.9em;
 }
 
 .article-image {
     width: 60px;
     height: 60px;
     object-fit: cover;
     border-radius: 5px;
     margin-right: 15px;
 }
 
 .article-actions {
     display: flex;
     gap: 10px;
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
 
 .no-articles {
     text-align: center;
     padding: 40px;
     color: #666;
     font-style: italic;
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
<!-- Barre de recherche -->
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
 <h2>Mes articles publiés (<?php echo count($articles); ?>)</h2>
 <!-- Messages de succès/erreur -->
 <?php if (isset($success_message)): ?>
     <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
 <?php endif; ?>
 
 <?php if (isset($error_message)): ?>
     <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
 <?php endif; ?>
 <!-- Bouton : ajouter un nouvel article -->
 <div style="margin-bottom: 20px; text-align: center;">
     <a href="ajouter_article.php" class="auth-btn primary">+ Ajouter un nouvel article</a>
 </div>
 
 <div class="card">
     <?php if (empty($articles)): ?>
         <div class="no-articles">
             <p>Vous n'avez publié aucun article pour le moment.</p>
             <a href="ajouter_article.php" class="auth-btn">Publier votre premier article</a>
         </div>
     <?php else: ?>
         <?php foreach ($articles as $article): ?>
             <div class="article-item">
                 <!-- Image article -->
                 <?php if (!empty($article['Photo_item'])): ?>
                     <img src="uploads/items/<?php echo htmlspecialchars($article['Photo_item']); ?>" 
                          alt="Photo de <?php echo htmlspecialchars($article['Nom_item']); ?>" 
                          class="article-image"
                          onerror="this.style.display='none'">
                 <?php else: ?>
                     <div class="article-image" style="background-color: #eee; display: flex; align-items: center; justify-content: center; color: #999;">
                         📷
                     </div>
                 <?php endif; ?>
                 <!-- info de l'article -->
                 <div class="article-info">
                     <div class="article-name"><?php echo htmlspecialchars($article['Nom_item']); ?></div>
                     <div class="article-details">
                         💰 <?php echo number_format($article['Prix_item'], 2); ?>€ | 
                         📂 <?php echo htmlspecialchars($article['Catégorie_item']); ?> | 
                         🏷️ <?php echo htmlspecialchars($article['Type_item']); ?>
                     </div>
                 </div>
                 <!-- Actions -->
                 <div class="article-actions">
                     <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                         <input type="hidden" name="article_id" value="<?php echo $article['ID_item']; ?>">
                         <button type="submit" name="delete_article" class="auth-btn danger">Supprimer</button>
                     </form>
                 </div>
             </div>
         <?php endforeach; ?>
     <?php endif; ?>
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

<script>
function editArticle(articleId) {
    // Fonction pour éditer un article (à implémenter)
    alert('Fonctionnalité d\'édition à venir pour l\'article ID: ' + articleId);
}
</script>
</body>
</html>