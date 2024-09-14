<?php
session_start(); 

include 'config.php';

if (!isset($_SESSION['NoUtilisateur'])) {
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
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
           
        </section>
    </div>
</body>
</html>