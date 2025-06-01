<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_POST['id_panier'])) {
    header("Location: panier.php");
    exit();
}

$idPanier = (int) $_POST['id_panier'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=agorafrancia;charset=utf8mb4", "root", "root");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //s'assurer que la ligne appartient bien à cet utilisateur
    $stmt = $pdo->prepare("
        DELETE FROM panier
        WHERE ID_panier = (
            SELECT ID_panier FROM (
                SELECT ID_panier FROM panier p
                JOIN acheteurs a ON p.ID_acheteurs = a.ID_acheteurs
                WHERE p.ID_panier = ? AND a.NomUtilisateur_acheteurs = ?
            ) AS sub
        )
    ");
    $stmt->execute([$idPanier, $_SESSION['username']]);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

header("Location: panier.php");
exit();
