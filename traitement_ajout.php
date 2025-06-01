<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ajouter_article.php");
    exit();
}
//verifier si utilisateur  connecte
if (!isset($_SESSION['username'])) {
    header("Location: connexion.php?error=" . urlencode("Vous devez être connecté pour ajouter un article."));
    exit();
}
try {
    $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //récup l'ID  vendeur connecté ou gérer si admin
    $ID_vendeurs = null;
    
    if ($_SESSION['username'] === 'admin') {
        //Admin peut publier : créer/récupérer un vendeur admin
        $admin_check = $pdo->prepare("SELECT ID_vendeurs FROM vendeurs WHERE NomUtilisateur_vendeurs = 'admin'");
        $admin_check->execute();
        $admin_vendeur = $admin_check->fetch(PDO::FETCH_ASSOC);
        
        if ($admin_vendeur) {
            // Vendeur admin existe déjà
            $ID_vendeurs = $admin_vendeur['ID_vendeurs'];
        } else {
            // Créer le vendeur admin avec auto-increment normal
            try {
                $create_admin = $pdo->prepare("
                    INSERT INTO vendeurs (NomUtilisateur_vendeurs, Nom_vendeurs, Email_vendeurs, mdp_vendeurs) 
                    VALUES ('admin', 'Administrateur', 'admin@agorafrancia.com', 'admin_password')
                ");
                $create_admin->execute();
                $ID_vendeurs = $pdo->lastInsertId();
            } catch (PDOException $e) {
                header("Location: ajouter_article.php?error=" . urlencode("Erreur lors de la création du vendeur admin : " . $e->getMessage()));
                exit();
            }
        }
    } else {
        //vérifier dans table vendeurs
        $stmt = $pdo->prepare("SELECT ID_vendeurs FROM vendeurs WHERE NomUtilisateur_vendeurs = ?");
        $stmt->execute([$_SESSION['username']]);
        $vendeur = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$vendeur) {
            header("Location: ajouter_article.php?error=" . urlencode("Utilisateur introuvable. Seuls les vendeurs et admin peuvent publier des articles."));
            exit();
        }
        
        $ID_vendeurs = $vendeur['ID_vendeurs'];
    }
    
    // Récupérer valider données du formulaire
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $categorie = trim($_POST['categorie'] ?? '');
    $type = trim($_POST['type'] ?? '');
    
    // Validation champs obligatoires
    if (empty($nom) || empty($description) || $prix <= 0 || empty($categorie) || empty($type)) {
        header("Location: ajouter_article.php?error=" . urlencode("Tous les champs obligatoires doivent être remplis."));
        exit();
    }
    
    // Traitement upload photo
    $photo_filename = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_name = $_FILES['photo']['name'];
        $photo_ext = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));
        
        // Vérifier extension
        $allowed_photo_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($photo_ext, $allowed_photo_extensions)) {
            header("Location: ajouter_article.php?error=" . urlencode("Format de photo non autorisé. Utilisez: jpg, jpeg, png, gif"));
            exit();
        }
        
        // Générer un nom unique pour la photo
        $photo_filename = 'item_' . $ID_vendeurs . '_' . time() . '.' . $photo_ext;
        $photo_path = 'uploads/items/' . $photo_filename;
        
        // Créer dossier si n'existe pas
        if (!is_dir('uploads/items/')) {
            mkdir('uploads/items/', 0777, true);
        }
        
        // Déplacer  fichier
        if (!move_uploaded_file($photo_tmp, $photo_path)) {
            header("Location: ajouter_article.php?error=" . urlencode("Erreur lors de l'upload de la photo."));
            exit();
        }
    } else {
        header("Location: ajouter_article.php?error=" . urlencode("La photo est obligatoire."));
        exit();
    }
    
    // Traitement upload vidéo comme phto
    $video_filename = '';
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $video_tmp = $_FILES['video']['tmp_name'];
        $video_name = $_FILES['video']['name'];
        $video_ext = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
        
        $allowed_video_extensions = ['mp4', 'avi', 'mov', 'wmv', 'webm'];
        if (!in_array($video_ext, $allowed_video_extensions)) {
            header("Location: ajouter_article.php?error=" . urlencode("Format de vidéo non autorisé. Utilisez: mp4, avi, mov, wmv, webm"));
            exit();
        }
        
        //limite 50Mb
        if ($_FILES['video']['size'] > 50 * 1024 * 1024) {
            header("Location: ajouter_article.php?error=" . urlencode("La vidéo est trop volumineuse (max 50MB)."));
            exit();
        }
        
        $video_filename = 'video_' . $ID_vendeurs . '_' . time() . '.' . $video_ext;
        $video_path = 'uploads/videos/' . $video_filename;
        
        if (!is_dir('uploads/videos/')) {
            mkdir('uploads/videos/', 0777, true);
        }
        
        if (!move_uploaded_file($video_tmp, $video_path)) {
            header("Location: ajouter_article.php?error=" . urlencode("Erreur lors de l'upload de la vidéo."));
            exit();
        }
    }
    
    // Générer un numéro item unique 
    $numeroID_item = 'ITEM_' . $ID_vendeurs . '_' . time();
    
    // Insérer article dans base de données
    $stmt = $pdo->prepare("
        INSERT INTO items (
            NumeroID_item, 
            Nom_item, 
            Photo_item, 
            Descriptions_item, 
            Vidéo_item, 
            Catégorie_item, 
            Type_item, 
            ID_vendeurs, 
            Prix_item
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $numeroID_item,
        $nom,
        $photo_filename,
        $description,
        $video_filename ?: null, 
        $categorie,
        $type,
        $ID_vendeurs,
        $prix
    ]);
    
    header("Location: gerer_articles.php?success=" . urlencode("Article ajouté avec succès !"));
    exit();
    
} catch (PDOException $e) {
    // Erreur base de données
    header("Location: ajouter_article.php?error=" . urlencode("Erreur de base de données : " . $e->getMessage()));
    exit();
} catch (Exception $e) {
    // Autres erreur
    header("Location: ajouter_article.php?error=" . urlencode("Erreur : " . $e->getMessage()));
    exit();
}
?>