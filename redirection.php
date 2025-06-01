<?php
session_start();

if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: compteAdmin.php");
        exit();
    } elseif ($_SESSION['user_type'] === 'vendeur') {
        header("Location: compteVendeur.php");
        exit();
    } elseif ($_SESSION['user_type'] === 'acheteur') {
        header("Location: paiement_formulaire.php");
        exit();
    }
} else {
    //utilisateur non connecté
    header("Location: connexion.html");
    exit();
}
?>
