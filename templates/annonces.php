<?php
include 'auth.php'; // Inclure le fichier d'authentification
checkAuth(); // Vérifier si l'utilisateur est authentifié
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Annonces</title>
    <link rel="stylesheet" href="../styles/styles.css"> <!-- Chemin vers le fichier CSS -->
</head>
<body>
    <header>
        <h1>Mes Annonces</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="auth.php?action=logout">Déconnexion</a></li> <!-- Lien vers la déconnexion -->
            </ul>
        </nav>
    </header>
    
    <main>
        <section id="annonces">
            <h2>Voici vos annonces :</h2>
            <!-- Logique pour afficher les annonces ici -->
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Mon Application d'Annonces</p>
    </footer>
</body>
</html>