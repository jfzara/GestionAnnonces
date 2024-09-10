<?php
session_start(); // Assurez-vous d'inclure session_start() ici

include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Tableau de bord</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <h1>Bienvenue sur votre tableau de bord</h1>
    <p><a href="logout.php">Se déconnecter</a></p>
</body>
</html>