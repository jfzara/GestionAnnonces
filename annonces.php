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

// Pagination
$limit = 5; // Nombre d'annonces par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Page actuelle
$offset = ($page - 1) * $limit; // Décalage pour la requête SQL

// Récupération des annonces avec pagination
$sql = "SELECT NoAnnonce, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Parution FROM annonces LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Récupérer le nombre total d'annonces pour la pagination
$total_sql = "SELECT COUNT(*) as total FROM annonces";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];
$total_pages = ceil($total / $limit); // Calculer le nombre total de pages
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Annonces</title>
    <link rel="stylesheet" href="styles.css">  
</head>
<body>
<nav class="navbar">
    <a href="annonces.php" class="nav-item">Annonces</a>
    <a href="gestion_annonces.php" class="nav-item">Gestion de vos annonces</a>
    <a href="modifier_profil.php" class="nav-item">Modification du profil</a>
    <a href="Deconnexion.php" class="nav-item">Déconnexion</a>
</nav>
<div id="divListe">
    <?php
    if ($result->num_rows > 0) {
        // Compteur pour le numéro séquentiel
        $sequentialNumber = 1 + $offset; // Commencer à partir de l'offset

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
                    <div class="text-right"><?php echo $row['NoAnnonce']; ?></div>
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

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">Précédent</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Suivant</a>
    <?php endif; ?>
</div>

</body>
</html>