<?php
session_start();

// config d'erreur pour debug 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'agorafrancia';
$user = 'root';
$pass = 'root'; 

//Connexion bdd
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function redirectWithError($message) {
    header("Location: connexion.html?error=" . urlencode($message));
    exit;
}
function redirectWithSuccess($message, $page) {
    header("Location: $page?success=" . urlencode($message));
    exit;
}
//traitement formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //récup données
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $userType = $_POST['user_type'] ?? 'acheteur';
    $rememberMe = isset($_POST['remember_me']);
    //validation des données
    if (empty($username)) {
        redirectWithError("Le nom d'utilisateur est requis.");
    }
    if (empty($password)) {
        redirectWithError("Le mot de passe est requis.");
    }
    // config selon type d'utilisateur
    $tableConfig = [
        'acheteur' => [
            'table' => 'Acheteurs',
            'username_field' => 'NomUtilisateur_acheteurs', 
            'password_field' => 'mdp_acheteurs',
            'id_field' => 'ID_acheteurs',
            'email_field' => 'Email_acheteurs',
            'redirect_page' => 'paiement_formulaire.php'
        ],
        'vendeur' => [
            'table' => 'Vendeurs',
            'username_field' => 'NomUtilisateur_vendeurs',
            'password_field' => 'mdp_vendeurs',
            'id_field' => 'ID_vendeurs',
            'email_field' => 'Email_vendeurs',
            'redirect_page' => 'compteVendeur.php'
        ],
        'admin' => [
            'table' => 'Administrateurs',
            'username_field' => 'NomUtilisateur_admin',
            'password_field' => 'mdp_admin',
            'id_field' => 'ID_admin',
            'email_field' => 'Email_admin',
            'redirect_page' => 'compteAdmin.php' 
        ]
    ];
    //vérifier si type d'utilisateur valide
    if (!isset($tableConfig[$userType])) {
        redirectWithError("Type d'utilisateur invalide.");
    }

    $config = $tableConfig[$userType];
    
    try {
        // Recherche de l'utilisateur dans la base de données
        // Pour les acheteurs, on peut chercher par nom OU par email
        if ($userType === 'acheteur') {
            $sql = "SELECT {$config['id_field']}, {$config['username_field']}, {$config['email_field']}, {$config['password_field']} 
                    FROM {$config['table']} 
                    WHERE {$config['username_field']} = ? OR {$config['email_field']} = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $username]);
        } else {
            $sql = "SELECT {$config['id_field']}, {$config['username_field']}, {$config['email_field']}, {$config['password_field']} 
                    FROM {$config['table']} 
                    WHERE {$config['username_field']} = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username]);
        }
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            redirectWithError("Nom d'utilisateur ou mot de passe incorrect.");
        }
        //vérification mdp
        if (!password_verify($password, $user[$config['password_field']])) {
            // Si le mdp haché ne fonctionne pas, vérifier le mdp en clair 
            if ($password !== $user[$config['password_field']]) {
                redirectWithError("Nom d'utilisateur ou mot de passe incorrect.");
            }
        }
        // Connexion réussie - créer la session
        $_SESSION['user_id'] = $user[$config['id_field']];
        $_SESSION['username'] = $user[$config['username_field']];
        $_SESSION['email'] = $user[$config['email_field']];
        $_SESSION['user_type'] = $userType;
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        //"Se souvenir de moi"
        if ($rememberMe) {
            // Cookie valide 30 jours
            $cookieData = [
                'user_id' => $user[$config['id_field']],
                'username' => $user[$config['username_field']],
                'user_type' => $userType,
                'token' => bin2hex(random_bytes(32)) 
            ];
            setcookie(
                'agora_remember', 
                base64_encode(json_encode($cookieData)), 
                time() + (30 * 24 * 60 * 60), 
                '/', 
                '', 
                false, 
                true 
            );
        }
        //redirection selon type d'utilisateur
        $welcomeMessage = "Bienvenue " . $user[$config['username_field']] . " !";
        redirectWithSuccess($welcomeMessage, $config['redirect_page']);
        
    } catch (PDOException $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        redirectWithError("Erreur technique lors de la connexion. Veuillez réessayer.");
    }
} else {
    // Si on accède directement au fichier PHP, rediriger vers le formulaire
    header("Location: connexion.html");
    exit;
}
?>