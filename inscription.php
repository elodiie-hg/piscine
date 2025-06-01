<?php

// config d'erreur pour debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'agorafrancia';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<!-- DEBUG: Connexion BDD réussie -->";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

//fonction de validation
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/[0-9]/', $password);
}

function redirectWithError($message) {
    header("Location: inscription.html?error=" . urlencode($message));
    exit;
}

function redirectWithSuccess($message) {
    header("Location: connexion.html?success=" . urlencode($message));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "<!-- DEBUG: Traitement POST démarré -->";
    echo "<!-- DEBUG: Données POST reçues: " . print_r($_POST, true) . " -->";
    
    //récup des données
    $username = trim($_POST['username'] ?? '');
    $nomPrenom = trim($_POST['nom'] ?? ''); 
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $termsAccepted = isset($_POST['terms']);
    
    echo "<!-- DEBUG: Variables après traitement -->";
    echo "<!-- Username: '$username' (longueur: " . strlen($username) . ") -->";
    echo "<!-- Nom Prénom: '$nomPrenom' (longueur: " . strlen($nomPrenom) . ") -->";
    echo "<!-- Email: '$email' -->";
    echo "<!-- Password length: " . strlen($password) . " -->";
    echo "<!-- Confirm Password length: " . strlen($confirmPassword) . " -->";
    echo "<!-- Terms accepted: " . ($termsAccepted ? 'OUI' : 'NON') . " -->";
    
    $errors = [];
    
    // Validation côté serveur
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    } elseif (strlen($username) > 50) {
        $errors[] = "Le nom d'utilisateur ne peut pas dépasser 50 caractères.";
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres, tirets et underscores.";
    }
    
    if (empty($nomPrenom)) {
        $errors[] = "Le nom et prénom sont requis.";
    } elseif (strlen($nomPrenom) < 2) {
        $errors[] = "Le nom et prénom doivent contenir au moins 2 caractères.";
    } elseif (strlen($nomPrenom) > 255) {
        $errors[] = "Le nom et prénom ne peuvent pas dépasser 255 caractères.";
    }
    
    if (empty($email)) {
        $errors[] = "L'adresse email est requise.";
    } elseif (!validateEmail($email)) {
        $errors[] = "L'adresse email n'est pas valide.";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    
    if (empty($confirmPassword)) {
        $errors[] = "La confirmation du mot de passe est requise.";
    } elseif ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    
    if (!$termsAccepted) {
        $errors[] = "Vous devez accepter les conditions d'utilisation.";
    }
    
    echo "<!-- DEBUG: Nombre d'erreurs de validation: " . count($errors) . " -->";
    if (!empty($errors)) {
        echo "<!-- DEBUG: Erreurs: " . implode(", ", $errors) . " -->";
    }
    
    // Vérification si email  unique
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM acheteurs WHERE Email_acheteurs = ?");
            $stmt->execute([$email]);
            $emailCount = $stmt->fetchColumn();
            echo "<!-- DEBUG: Emails existants avec '$email': $emailCount -->";
            
            if ($emailCount > 0) {
                $errors[] = "Cette adresse email est déjà utilisée.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la vérification de l'email.";
            echo "<!-- DEBUG: Erreur vérification email: " . $e->getMessage() . " -->";
        }
    }
    
    // Vérification si nom utilisateur  unique
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM acheteurs WHERE NomUtilisateur_acheteurs = ?");
            $stmt->execute([$username]);
            $usernameCount = $stmt->fetchColumn();
            echo "<!-- DEBUG: Usernames existants avec '$username': $usernameCount -->";
            
            if ($usernameCount > 0) {
                $errors[] = "Ce nom d'utilisateur est déjà pris.";
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la vérification du nom d'utilisateur.";
            echo "<!-- DEBUG: Erreur vérification username: " . $e->getMessage() . " -->";
        }
    }
    
    // si erreurs, rediriger avec message d'erreur
    if (!empty($errors)) {
        echo "<!-- DEBUG: Redirection avec erreurs: " . implode(" | ", $errors) . " -->";
        redirectWithError(implode(" ", $errors));
    }
    
    // Hasher pour sécuriter
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    echo "<!-- DEBUG: Mot de passe hashé généré (longueur: " . strlen($hashedPassword) . ") -->";
    
    //inscription utilisateur
    try {
        echo "<!-- DEBUG: Tentative d'insertion en base -->";
        $sql = "INSERT INTO acheteurs (NomUtilisateur_acheteurs, Nom_acheteurs, Email_acheteurs, mdp_acheteurs) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        echo "<!-- DEBUG: Requête SQL: $sql -->";
        echo "<!-- DEBUG: Paramètres insertion: Username='$username', Nom='$nomPrenom', Email='$email', Password=[HASHÉ] -->";
        $result = $stmt->execute([$username, $nomPrenom, $email, $hashedPassword]);
        echo "<!-- DEBUG: Résultat execute(): " . ($result ? 'TRUE' : 'FALSE') . " -->";
        echo "<!-- DEBUG: Lignes affectées: " . $stmt->rowCount() . " -->";
        if ($result && $stmt->rowCount() > 0) {
            $newUserId = $pdo->lastInsertId();
            echo "<!-- DEBUG: Nouvel utilisateur créé avec ID: $newUserId -->";
            echo "<!-- DEBUG: Redirection vers succès -->";
            redirectWithSuccess("Compte créé avec succès ! Vous pouvez maintenant vous connecter.");
        } else {
            echo "<!-- DEBUG: Aucune ligne insérée malgré execute() TRUE -->";
            redirectWithError("Erreur lors de la création du compte - aucune donnée insérée.");
        }
    } catch (PDOException $e) {
        echo "<!-- DEBUG: Exception PDO: " . $e->getMessage() . " -->";
        echo "<!-- DEBUG: Code erreur: " . $e->getCode() . " -->";
        echo "<!-- DEBUG: Info erreur: " . print_r($e->errorInfo, true) . " -->";
        
        if ($e->getCode() == 23000 || strpos($e->getMessage(), 'Duplicate entry') !== false) {
            redirectWithError("Cette adresse email ou ce nom d'utilisateur existe déjà.");
        } else {
            redirectWithError("Erreur technique lors de l'inscription. Veuillez réessayer.");
        }
    }
    
} else {
    echo "<!-- DEBUG: Accès direct au PHP sans POST, redirection -->";
    header("Location: inscription.html");
    exit;
}
?>