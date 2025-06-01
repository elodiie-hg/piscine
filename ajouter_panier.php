<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_item'])) {
    $idItem = intval($_POST['ID_item']);
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=agorafrancia;charset=utf8mb4', 'root', 'root');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //vérifie si utilisateur est acheteur ou pas 
        $stmt = $pdo->prepare("SELECT ID_acheteurs FROM acheteurs WHERE NomUtilisateur_acheteurs = ?");
        $stmt->execute([$_SESSION['username']]);
        $acheteur = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($acheteur) {
            $idAcheteur = $acheteur['ID_acheteurs'];
            //vérifie si produit est déjà dans le panier
            $check = $pdo->prepare("SELECT * FROM panier WHERE ID_acheteurs = ? AND ID_item = ?");
            $check->execute([$idAcheteur, $idItem]);
            if ($check->rowCount() == 0) {
                //mettre dans panier
                $insert = $pdo->prepare("INSERT INTO panier (ID_acheteurs, ID_item) VALUES (?, ?)");
                $insert->execute([$idAcheteur, $idItem]);
            }
            header('Location: panier.php?ajout=ok');
            exit();
        } else {
            echo "Utilisateur non reconnu comme acheteur.";
        }
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
}
