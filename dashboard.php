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

    <!-- Menu principal -->
    <nav>
        <ul>
            <li><a href="annonces.php">Afficher les annonces</a></li>
            <li><a href="gestion_annonces.php">Gérer les annonces</a></li>
            <li><a href="modification_profil.php">Modifier le profil</a></li>
            <li><a href="logout.php">Se déconnecter</a></li>
        </ul>
    </nav>

    <!-- Contenu par défaut : Affichage de toutes les annonces -->
    <section id="content">
        <h2>Liste des annonces</h2>
        <!-- Ici tu peux inclure le code pour afficher les annonces -->
    </section>
</body>
</html>