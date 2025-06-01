<?php
session_start();
session_destroy();
header("Location: connexion.html?success=" . urlencode("Déconnexion réussie."));
exit();
