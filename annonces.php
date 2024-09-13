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

// Récupération des annonces
$sql = "SELECT NoAnnonce, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Parution FROM annonces";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annonces</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Lier Bootstrap -->
</head>
<body>

<div id="divListe" class="d-flex flex-wrap justify-content-around mt-2 border-secondary">
    <?php
    if ($result->num_rows > 0) {
        // Compteur pour le numéro séquentiel
        $sequentialNumber = 1;

        // Affichage des annonces
        while ($row = $result->fetch_assoc()) {
            $datePublication = date('Y-m-d H:i', strtotime($row['Parution']));
            $photoUrl = !empty($row['Photo']) ? $row['Photo'] : 'default.jpg'; // Par défaut si pas de photo
            ?>
            <div id="divAnnonce-<?php echo $row['NoAnnonce']; ?>" class="m-3">
                <div class="card annonce">
                    <div class="card-header d-flex justify-content-between py-1">
                        <div class="text-left"><?php echo $sequentialNumber++; ?></div>
                        <div class="text-right">Catégorie : <?php echo $row['Categorie']; ?></div>
                    </div>
                    <div class="overflow-hidden text-right imageSize">
                        <img alt="Image de <?php echo $row['DescriptionAbregee']; ?>" src="<?php echo $photoUrl; ?>" width="144" class="m-auto" style="height: auto;">
                    </div>
                    <div class="card-body pb-1">
                        <h6 class="card-title">
                            <a href="Annonce.php?id=<?php echo $row['NoAnnonce']; ?>"><?php echo $row['DescriptionAbregee']; ?></a>
                        </h6>
                        <p><?php echo $row['DescriptionComplete']; ?></p>
                        <div class="text-right font-weight-bold">
                            <span>
                                <?php echo !empty($row['Prix']) ? number_format($row['Prix'], 2, '.', ' ') . " $" : 'N/A'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between py-0">
                        <div class="text-left">Publié le : <?php echo $datePublication; ?></div>
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

<script src="path/to/bootstrap.bundle.js"></script> 
</body>
</html>