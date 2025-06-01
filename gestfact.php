<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //stocker infos du formulaire dans la session
    $_SESSION['facture'] = $_POST;
    //redirection vers page de facture
    header("Location: factures.php");
    exit();
} else {
    //accès direct interdit
    echo "Accès non autorisé.";
    exit();
}
