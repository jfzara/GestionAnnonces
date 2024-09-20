<?php
// Inclure le fichier contenant la fonction getCategoryName
include 'annonces.php'; // Assurez-vous que le chemin est correct

// Connexion à la base de données
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'gestionannonces';

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die('Échec de la connexion: ' . $conn->connect_error);
}

// Vérifier si l'ID de l'annonce est passé dans l'URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convertir en entier pour éviter les injections SQL

    // Récupérer les détails de l'annonce
    $sql = "SELECT * FROM annonces WHERE NoAnnonce = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $annonce = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Détails de l'annonce</title>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body>
            <nav class='navbar'>
                <a href='annonces.php' class='nav-item'>Annonces</a>
                <a href='gestion_annonces.php' class='nav-item'>Gestion de vos annonces</a>
                <a href='modifier_profil.php' class='nav-item'>Modification du profil</a>
                <a href='logout.php' class='nav-item'>Déconnexion</a>
            </nav>
            <div class="annonce-detail">
                <h2><?php echo $annonce['DescriptionAbregee']; ?></h2>
                <img src="<?php echo !empty($annonce['Photo']) ? $annonce['Photo'] : 'default.jpg'; ?>" alt="Image de l'annonce">
                <p><strong>Description Complète:</strong> <?php echo $annonce['DescriptionComplete']; ?></p>
                <p><strong>Prix:</strong> <?php echo !empty($annonce['Prix']) ? number_format($annonce['Prix'], 2, '.', ' ') . " $" : 'N/A'; ?></p>
                <p><strong>Catégorie:</strong> <?php echo getCategoryName($annonce['Categorie']); ?></p>
                <p><strong>Date de parution:</strong> <?php echo date('Y-m-d', strtotime($annonce['Parution'])); ?></p>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<p>Aucune annonce trouvée.</p>";
    }
} else {
    echo "<p>ID de l'annonce non spécifié.</p>";
}

// Fermer la connexion
$conn->close();
?>