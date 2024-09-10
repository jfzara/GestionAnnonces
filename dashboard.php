<?php
// dashboard.php
include 'config.php'; // Inclure le fichier de configuration

if (!isset($_SESSION['user_id'])) { // Vérifier si l'utilisateur est connecté
    header("Location: login.php"); // Rediriger vers la page de connexion si non connecté
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
    <h1>Bienvenue sur le tableau de bord</h1>
    <p><a href="logout.php">Se déconnecter</a></p> <!-- Lien pour se déconnecter -->
</body>
</html>