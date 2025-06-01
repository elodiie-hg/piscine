<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUsername() {
    return $_SESSION['username'] ?? null;
}

function getUserEmail() {
    return $_SESSION['email'] ?? null;
}

function isAdmin() {
    return isLoggedIn() && getUserType() === 'admin';
}

function isVendeur() {
    return isLoggedIn() && getUserType() === 'vendeur';
}

function isAcheteur() {
    return isLoggedIn() && getUserType() === 'acheteur';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: connexion.html?error=" . urlencode("Vous devez être connecté pour accéder à cette page."));
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: accueil.php?error=" . urlencode("Accès réservé aux administrateurs."));
        exit;
    }
}

function requireVendeur() {
    requireLogin();
    if (!isVendeur()) {
        header("Location: accueil.php?error=" . urlencode("Accès réservé aux vendeurs."));
        exit;
    }
}

function checkRememberMeCookie() {
    if (!isLoggedIn() && isset($_COOKIE['agora_remember'])) {
        try {
            $cookieData = json_decode(base64_decode($_COOKIE['agora_remember']), true);
            
            if ($cookieData && isset($cookieData['user_id']) && isset($cookieData['username']) && isset($cookieData['user_type'])) {
                $_SESSION['user_id'] = $cookieData['user_id'];
                $_SESSION['username'] = $cookieData['username'];
                $_SESSION['user_type'] = $cookieData['user_type'];
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                
                return true;
            }
        } catch (Exception $e) {
            setcookie('agora_remember', '', time() - 3600, '/');
        }
    }
    
    return false;
}

checkRememberMeCookie();

function getLoginDuration() {
    if (isset($_SESSION['login_time'])) {
        $duration = time() - $_SESSION['login_time'];
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        return sprintf("%02d:%02d", $hours, $minutes);
    }
    return "00:00";
}

function renewSession() {
    if (isLoggedIn()) {
        session_regenerate_id(true);
    }
}
?>