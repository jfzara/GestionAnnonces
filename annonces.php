<?php
// Connexion à la base de données
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "gestionannonces"; 

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// Fonction pour obtenir le nom de la catégorie
function getCategoryName($categoryNumber) {
    switch ($categoryNumber) {
        case 1:
            return 'Location';
        case 2:
            return 'Recherche';
        case 3:
            return 'À vendre';
        case 4:
            return 'À donner';
        case 5:
            return 'Service offert';
        case 6:
            return 'Autre';
        default:
            return 'Inconnue';
    }
}

// Récupération des annonces
$sql = "SELECT NoAnnonce, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Parution FROM annonces";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annonces</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lier votre fichier de style -->
</head>
<body>

<div id="divListe" >
    <?php
    if ($result->num_rows > 0) {
        // Compteur pour le numéro séquentiel
        $sequentialNumber = 1;

        // Affichage des annonces
        while ($row = $result->fetch_assoc()) {
            $datePublication = date('Y-m-d H:i', strtotime($row['Parution']));
            $photoUrl = !empty($row['Photo']) ? $row['Photo'] : 'default.jpg'; // Par défaut si pas de photo
            ?>
            <div id="divAnnonce-<?php echo $row['NoAnnonce']; ?>" class="annonce">
                <div class="annonce-header">
                    <div class="text-left"><?php echo $sequentialNumber++; ?></div>
                    <div class="text-right"><?php echo getCategoryName($row['Categorie']); ?></div>
                </div>
                <div class="annonce-image">
                    <img alt="Image de <?php echo $row['DescriptionAbregee']; ?>" src="<?php echo $photoUrl; ?>" width="144" class="m-auto" style="height: auto;">
                </div>
                <div class="annonce-body">
                    <h6 class="annonce-title">
                        <a href="Annonce.php?id=<?php echo $row['NoAnnonce']; ?>"><?php echo $row['DescriptionAbregee']; ?></a>
                    </h6>
                    <p class="non-gras"><?php echo $row['DescriptionComplete']; ?></p>
                    <div class="text-right font-weight-bold">
                        <span>
                            <?php echo !empty($row['Prix']) ? number_format($row['Prix'], 2, '.', ' ') . " $" : 'N/A'; ?>
                        </span>
                    </div>
                </div>
                <div class="annonce-footer">
                    <div class="text-left"><?php echo $datePublication; ?></div>
                    <div class="text-right"><?php echo $row['NoAnnonce']; ?></div> <!-- Numéro d'incrément -->
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

</body>
</html>