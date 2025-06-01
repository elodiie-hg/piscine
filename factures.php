<?php
session_start();

if (!isset($_SESSION['facture'])) {
    echo "Aucune donnée de commande trouvée.";
    exit();
}

$data = $_SESSION['facture'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Facture - Agora Francia</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      max-width: 800px;
      margin: 20px auto;
      padding: 20px;
      background: #f9f9f9;
      border: 1px solid #ccc;
      border-radius: 10px;
    }
    h1 {
      color: #397194;
      text-align: center;
    }
    .facture-section {
      margin-top: 20px;
    }
    .facture-section h2 {
      border-bottom: 1px solid #397194;
      padding-bottom: 5px;
    }
    .facture-section p {
      margin: 5px 0;
    }
    .highlight {
      font-weight: bold;
    }
  </style>
</head>
<body>

  <h1>🧾 Facture de votre commande</h1>

  <div class="facture-section">
    <h2>📦 Livraison</h2>
    <p><span class="highlight">Nom :</span> <?= htmlspecialchars($data['prenom'] . ' ' . $data['nom']) ?></p>
    <p><span class="highlight">Adresse :</span> <?= htmlspecialchars($data['adresse1']) ?> <?= htmlspecialchars($data['adresse2']) ?></p>
    <p><span class="highlight">Ville :</span> <?= htmlspecialchars($data['ville']) ?>, <?= htmlspecialchars($data['code_postal']) ?>, <?= htmlspecialchars($data['pays']) ?></p>
    <p><span class="highlight">Téléphone :</span> <?= htmlspecialchars($data['telephone']) ?></p>
    <p><span class="highlight">Email :</span> <?= htmlspecialchars($data['email']) ?></p>
  </div>

  <div class="facture-section">
    <h2>💳 Paiement</h2>
    <p><span class="highlight">Type de carte :</span> <?= htmlspecialchars(ucfirst($data['type_carte'])) ?></p>
    <p><span class="highlight">Nom sur la carte :</span> <?= htmlspecialchars($data['nom_carte']) ?></p>
    <p><span class="highlight">Carte n° :</span> **** **** **** <?= substr(preg_replace('/\D/', '', $data['numero_carte']), -4) ?></p>
    <p><span class="highlight">Expiration :</span> <?= htmlspecialchars($data['date_expiration']) ?></p>
  </div>

  <div class="facture-section" style="margin-top: 30px;">
    <p>✅ Merci pour votre achat sur <strong>Agora Francia</strong> !</p>
    <p style="color: #555; font-size: 0.9em;">Cette facture est générée automatiquement. Une copie vous a été envoyée par e-mail si vous avez fourni une adresse valide.</p>
  </div>

</body>
</html>
