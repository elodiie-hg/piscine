 <?php
session_start();

//traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        
        //validation des champs
        if (empty($username)) {
            $error_message = "Veuillez remplir le nom d'utilisateur.";
        } elseif (isset($_POST['demote_to_buyer']) && empty($email)) {
            $error_message = "L'email est requis pour passer en acheteur.";
        } else {
            
            //promouvoir acheteur en vendeur
            if (isset($_POST['promote_to_vendor'])) {
                //rechercher l'utilisateur dans la table acheteurs
                $stmt = $pdo->prepare("SELECT * FROM acheteurs WHERE Email_acheteurs = ? AND NomUtilisateur_acheteurs = ?");
                $stmt->execute([$email, $username]);
                $acheteur = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($acheteur) {
                    //vérifier qu'il n'est pas déjà vendeur
                    $check_stmt = $pdo->prepare("SELECT ID_vendeurs FROM vendeurs WHERE Email_vendeurs = ?");
                    $check_stmt->execute([$acheteur['Email_acheteurs']]);
                    
                    if ($check_stmt->fetch()) {
                        $error_message = "Cet utilisateur est déjà un vendeur.";
                    } else {
                        //commencer une transaction
                        $pdo->beginTransaction();
                        
                        try {
                            //insérer dans la table vendeurs en copiant les infos
                            $insert_stmt = $pdo->prepare("
                                INSERT INTO vendeurs (
                                    Nom_vendeurs, 
                                    Email_vendeurs, 
                                    mdp_vendeurs,
                                    NomUtilisateur_vendeurs
                                ) VALUES (?, ?, ?, ?)
                            ");
                            $insert_stmt->execute([
                                $acheteur['Nom_acheteurs'],        // Nom_acheteurs → Nom_vendeurs
                                $acheteur['Email_acheteurs'],      // Email_acheteurs → Email_vendeurs
                                $acheteur['mdp_acheteurs'],        // mdp_acheteurs → mdp_vendeurs
                                $acheteur['NomUtilisateur_acheteurs']         // Nom_acheteurs → NomUtilisateur_vendeurs (en attendant clarification)
                            ]);
                            
                            //supp de la table acheteurs
                            $delete_stmt = $pdo->prepare("DELETE FROM acheteurs WHERE ID_acheteurs = ?");
                            $delete_stmt->execute([$acheteur['ID_acheteurs']]);
                            
                            // Valider la transaction
                            $pdo->commit();
                            $success_message = "Utilisateur '$username' promu vendeur avec succès !";
                            
                        } catch (Exception $e) {
                            $pdo->rollback();
                            $error_message = "Erreur lors de la promotion : " . $e->getMessage();
                        }
                    }
                } else {
                    $error_message = "Acheteur introuvable avec ces informations.";
                }
            }
            
            //passer de vendeur à acheteur
            elseif (isset($_POST['demote_to_buyer'])) {
                //rechercher utilisateur dans la table vendeurs
                $stmt = $pdo->prepare("SELECT * FROM vendeurs WHERE Email_vendeurs = ? AND NomUtilisateur_vendeurs = ?");
                $stmt->execute([$email, $username]);
                $vendeur = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($vendeur) {
                    //verifier qu'il n'est pas déjà acheteur
                    $check_stmt = $pdo->prepare("SELECT ID_acheteurs FROM acheteurs WHERE Email_acheteurs = ?");
                    $check_stmt->execute([$vendeur['Email_vendeurs']]);
                    
                    if ($check_stmt->fetch()) {
                        $error_message = "Cet utilisateur est déjà un acheteur.";
                    } else {
                        //vérifier s'il a des articles en vente
                        $articles_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM items WHERE ID_vendeurs = ?");
                        $articles_stmt->execute([$vendeur['ID_vendeurs']]);
                        $articles_count = $articles_stmt->fetch()['count'];
                        
                        if ($articles_count > 0) {
                            $error_message = "Impossible de rétrograder : ce vendeur a encore $articles_count article(s) en vente.";
                        } else {
                            //commencer une transaction
                            $pdo->beginTransaction();
                            
                            try {
                                //insérer dans la table acheteurs en copiant les infos
                                $insert_stmt = $pdo->prepare("
                                    INSERT INTO acheteurs (
                                        Nom_acheteurs, 
                                        Email_acheteurs, 
                                        mdp_acheteurs,
                                        NomUtilisateur_acheteurs
                                    ) VALUES (?, ?, ?, ?)
                                ");
                                $insert_stmt->execute([
                                    $vendeur['Nom_vendeurs'],          // Nom_vendeurs → Nom_acheteurs
                                    $vendeur['Email_vendeurs'],        // Email_vendeurs → Email_acheteurs
                                    $vendeur['mdp_vendeurs'],           // mdp_vendeurs → mdp_acheteurs
                                    $vendeur['NomUtilisateur_vendeurs']           // NomUtilisateur_vendeurs→ NomUtilisateur_acheteurs
                                    
                                ]);
                                
                                //supp de la table vendeurs
                                $delete_stmt = $pdo->prepare("DELETE FROM vendeurs WHERE ID_vendeurs = ?");
                                $delete_stmt->execute([$vendeur['ID_vendeurs']]);
                                
                                //valider la transaction
                                $pdo->commit();
                                $success_message = "Utilisateur '$username' rétrogradé acheteur avec succès !";
                                
                            } catch (Exception $e) {
                                $pdo->rollback();
                                $error_message = "Erreur lors de la rétrogradation : " . $e->getMessage();
                            }
                        }
                    }
                } else {
                    $error_message = "Vendeur introuvable avec ces informations.";
                }
            }
        }
        
    } catch (PDOException $e) {
        $error_message = "Erreur de base de données : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
 <meta charset="UTF-8" />
 <title>Gestion d'utilisateur - Agora Francia</title>
 <link rel="stylesheet" href="styles.css" />
 <style>
.gestion-wrapper {
 margin-top: 120px;
 display: flex;
 flex-direction: column;
 align-items: center;
 }
.gestion-header {
 font-size: 32px;
 color: #303890;
 font-weight: bold;
 margin-bottom: 40px;
 border: 2px solid #303890;
 padding: 15px 30px;
 border-radius: 10px;
 background-color: #f9f9f9;
 }
.gestion-container {
 display: flex;
 justify-content: space-between;
 gap: 60px;
 width: 80%;
 max-width: 1000px;
 }
.gestion-form {
 flex: 1;
 display: flex;
 flex-direction: column;
 gap: 20px;
 }
.gestion-form input {
 padding: 10px;
 font-size: 16px;
 border: 1px solid #ccc;
 border-radius: 8px;
 width: 100%;
 }
.gestion-actions {
 flex: 1;
 display: flex;
 flex-direction: column;
 gap: 20px;
 align-items: stretch;
 justify-content: flex-start;
 }
.gestion-actions button {
 padding: 15px;
 font-size: 16px;
 background-color: #273482;
 color: white;
 border: none;
 border-radius: 8px;
 cursor: pointer;
 transition: background-color 0.3s ease;
 }
.gestion-actions button:hover {
 background-color: #0066cc;
 }
.alert {
 padding: 15px;
 margin: 20px 0;
 border-radius: 5px;
 font-weight: 500;
 max-width: 800px;
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
 <div class="sidebar-toggle" onclick="toggleSidebar()">&#9776;</div>
 <div id="sidebar">
 <div class="close-btn" onclick="toggleSidebar()">×</div>
 <nav>
 <ul>
 <li><a href="accueil.php">Accueil</a></li>
 <li><a href="toutparcourir.php">Tout parcourir</a></li>
 <li><a href="notifications.php">Notifications</a></li>
 <li><a href="contact.php">Votre compte</a></li>
 </ul>
 </nav>
 </div>
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
 <div class="gestion-wrapper">
 <div class="gestion-header">Gestion d'utilisateur</div>
 
 <!-- Messages de succès/erreur -->
 <?php if (isset($success_message)): ?>
     <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
 <?php endif; ?>
 
 <?php if (isset($error_message)): ?>
     <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
 <?php endif; ?>
 
 <div class="gestion-container">
 <form class="gestion-form" method="POST">
 <label for="username">Nom d'utilisateur :</label>
 <input type="text" id="username" name="username" placeholder="Nom utilisateur" required />
 <label for="email">Adresse email :</label>
 <input type="email" id="email" name="email" placeholder="exemple@agora.fr" />
 
 <div class="gestion-actions">
 <button type="submit" name="promote_to_vendor" onclick="return confirm('Êtes-vous sûr de vouloir promouvoir cet utilisateur en vendeur ?')">
     Promouvoir vendeur
 </button>
 <button type="submit" name="demote_to_buyer" onclick="return confirm('Êtes-vous sûr de vouloir rétrograder cet utilisateur en acheteur ?')">
     Passer acheteur
 </button>
 </div>
 </form>
 </div>
 </div>
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
function toggleSidebar() {
const sidebar = document.getElementById('sidebar');
sidebar.classList.toggle('active');
sidebar.style.top = "80px";
sidebar.style.height = "calc(100% - 80px)";
 }
</script>
</body>
</html>