<?php
// Connexion à la base de données
$servername = "localhost"; // Remplacez par votre serveur
$username = "root"; // Utilisateur par défaut pour WAMP
$password = ""; // Mot de passe par défaut pour WAMP
$dbname = "gestionannonces"; // Remplacez par le nom de votre base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// Récupération des annonces
$sql = "SELECT * FROM annonces";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annonces</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Assurez-vous de lier Bootstrap -->
</head>
<body>

<div id="divListe" class="d-flex flex-wrap justify-content-around mt-2 border-secondary">
    <?php
    if ($result->num_rows > 0) {
        // Compteur pour le numéro séquentiel
        $sequentialNumber = 1;

        // Affichage des annonces
        while ($row = $result->fetch_assoc()) {
            $datePublication = date('Y-m-d H:i', strtotime($row['date_creation']));
            $fullName = $row['auteur_nom'] . ', ' . $row['auteur_prenom']; // Assurez-vous que ces colonnes existent
            $isCurrentUser = (isset($_SESSION['user_email']) && $row['auteur_email'] === $_SESSION['user_email']); // Vérifie si l'utilisateur est connecté
            
            ?>
            <div id="divAnnonce-<?php echo $row['id']; ?>" class="m-3">
                <div class="card annonce">
                    <div class="card-header d-flex justify-content-between py-1">
                        <div class="text-left"><?php echo $sequentialNumber++; ?></div>
                        <div class="text-right"><?php echo $row['categorie']; ?></div>
                    </div>
                    <div class="overflow-hidden text-right imageSize">
                        <img alt="Image de <?php echo $row['titre']; ?>" src="<?php echo $row['image_url']; ?>" width="144" class="m-auto" style="height: auto;">
                    </div>
                    <div class="card-body pb-1">
                        <h6 class="card-title">
                            <a href="Annonce.php?id=<?php echo $row['id']; ?>"><?php echo $row['titre']; ?></a>
                        </h6>
                        <div class="text-left">
                            <?php if (!$isCurrentUser): ?>
                                <a href="mailto:<?php echo $row['contact_email']; ?>"><?php echo $fullName; ?></a>
                            <?php else: ?>
                                <?php echo $fullName; ?>
                            <?php endif; ?>
                        </div>
                        <div class="text-right font-weight-bold">
                            <span>
                                <?php echo !empty($row['prix']) ? number_format($row['prix'], 2, '.', ' ') . " $" : 'N/A'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between py-0">
                        <div class="text-left"><?php echo $datePublication; ?></div>
                        <div class="text-right font-italic">1</div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>Aucune annonce trouvée.</p>";
    }
    $conn->close();
    ?>
</div>

<script src="path/to/bootstrap.bundle.js"></script> <!-- Assurez-vous de lier le script Bootstrap -->
</body>
</html>