<?php
// logout.php
include 'config.php'; // Inclure le fichier de configuration
session_destroy(); // Détruire la session
header("Location: login.php"); // Rediriger vers la page de connexion
exit();
?>