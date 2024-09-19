<?php
// Connexion à la base de données
$servername = 'localhost';

$username = 'root';

$password = '';

$dbname = 'gestionannonces';

// Création de la connexion
$conn = new mysqli( $servername, $username, $password, $dbname );

// Vérification de la connexion
if ( $conn->connect_error ) {
    die( 'Échec de la connexion: ' . $conn->connect_error );
}

// Fonction pour obtenir le nom de la catégorie

function getCategoryName( $categoryNumber ) {
    switch ( $categoryNumber ) {
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
$limit = 5;
// Nombre d'annonces par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Page actuelle
$offset = ($page - 1) * $limit; // Décalage pour la requête SQL

// Récupération des annonces avec pagination
$sql = "SELECT NoAnnonce, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Parution FROM annonces LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Récupérer le nombre total d'annonces pour la pagination
$total_sql = 'SELECT COUNT(*) as total FROM annonces';
$total_result = $conn->query( $total_sql );
$total_row = $total_result->fetch_assoc();
$total = $total_row[ 'total' ];
$total_pages = ceil( $total / $limit );
// Calculer le nombre total de pages
?>

<!DOCTYPE html>
<html lang = 'fr'>
<head>
<meta charset = 'UTF-8'>
<title>Annonces</title>
<link rel = 'stylesheet' href = 'styles.css'>
</head>
<body>
<nav class = 'navbar'>
<a href = 'annonces.php' class = 'nav-item'>Annonces</a>
<a href = 'gestion_annonces.php' class = 'nav-item'>Gestion de vos annonces</a>
<a href = 'modifier_profil.php' class = 'nav-item'>Modification du profil</a>
<a href = 'logout.php' class = 'nav-item'>Déconnexion</a>
</nav>
<div id = 'divRecherche' style = "width: 90%; display: flex; justify-content: space-between; padding: 10px;position: relative;
    top: -13vh;">
<!-- Eléments par page -->
<div id = 'divNbParPage' style = 'flex: 1; display: flex; flex-direction: column; align-items: flex-start;'>
<div style = 'display: flex; align-items: center; margin-bottom: 10px;'>
<label style = 'margin-right: 10px;'>Éléments par page :</label>
<select id = 'ddlNbParPage' style = 'width: 60px; padding: 5px;'>
<option value = '5'>5</option>
<option value = '10'>10</option>
<option value = '15'>15</option>
<option value = '20'>20</option>
</select>
</div>
<h5 style = 'color: gray; font-style: italic;'><?php echo $total;
?> annonces trouvées.</h5>
</div>

<!-- Espace vide au milieu -->
<div style = 'flex: 1;'></div>

<!-- Recherche simple -->
<div id = 'divRechercheSimple' style = 'flex: 2; display: flex; flex-direction: column; align-items: flex-end;'>
<form method = 'POST' action = ''>
<div style = 'display: flex; align-items: center; margin-bottom: 10px;'>
<label style = 'margin-right: 10px;'>Ordre :</label>
<select id = 'TypeOrdre' name = 'criteria' style = 'padding: 5px; margin-right: 10px;'>
<option value = 'date'>Date</option>
<option value = 'auteur'>Auteur</option>
<option value = 'categorie'>Catégorie</option>
</select>
<select id = 'Ordre' name = 'Ordre' style = 'padding: 5px; margin-right: 2vw;'>
<option value = 'ASC'>▲</option>
<option value = 'DESC'>▼</option>
</select>
<div style = 'display: flex; align-items: center; margin-bottom: 10px;'>
<input type = 'text' id = 'Description' name = 'searchTerm' placeholder = 'Recherche...' style = 'padding: 5px; margin-right: 10px; margin-top: 1vh;'>
</div>
</div>

<div style = 'display: flex; align-items: center;'>
<input type = 'submit' value = 'Rechercher' style = 'margin-right: 10px; background-color: #007bff; color: white; font-weight: bold;'>
<button id = 'btnAfficherAvance' type = 'button' onclick = 'toggleRechercheAvance();' style = 'font-weight: bold; background-color: gray; font-size: 1rem;'>+</button>
</div>
</form>
<?php
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

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['criteria']) && isset($_POST['searchTerm'])) {
    $criteria = $_POST['criteria'];
    $searchTerm = $conn->real_escape_string($_POST['searchTerm']);

    // Construire la requête SQL en fonction du critère sélectionné
    switch ($criteria) {
        case 'date':
            // Format de recherche pour la date
            $sql = "SELECT * FROM annonces WHERE Parution LIKE '%$searchTerm%'";
            break;
        case 'auteur':
            // Recherche par NoUtilisateur
            if (is_numeric($searchTerm)) {
                $sql = "SELECT * FROM annonces WHERE NoUtilisateur = $searchTerm";
            } else {
                $sql = "SELECT * FROM annonces WHERE NoUtilisateur LIKE '%$searchTerm%'";
            }
            break;
        case 'categorie':
            // Recherche par Categorie
            if (is_numeric($searchTerm)) {
                $sql = "SELECT * FROM annonces WHERE Categorie = $searchTerm";
            } else {
                $sql = "SELECT * FROM annonces WHERE Categorie LIKE '%$searchTerm%'";
            }
            break;
        default:
            $sql = 'SELECT * FROM annonces'; // Valeur par défaut
            break;
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Afficher les annonces pertinentes
        echo "<table border='1'>";
        echo '<tr><th>NoAnnonce</th><th>Parution</th><th>Catégorie</th><th>DescriptionAbregee</th><th>Prix</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['NoAnnonce']}</td>
                    <td>{$row['Parution']}</td>
                    <td>{$row['Categorie']}</td> <!-- Vous pouvez remplacer par getCategoryName si nécessaire -->
                    <td>{$row['DescriptionAbregee']}</td>
                    <td>{$row['Prix']}</td>
                  </tr>";
        }
        echo '</table>';
    } else {
        echo 'Aucune annonce ne correspond à votre recherche.';
    }
}

// Fermer la connexion
$conn->close();
?>
</div>
</div>

<!-- Recherche avancée ( masquée par défaut ) -->
<div id = 'divRechercheAvancé' style = 'margin-top: 10px; padding: 10px; border: 1px solid #000; display: none;'>
<div style = 'margin-bottom: 10px;'>
<label for = 'Auteur' style = 'width: 100px; display: inline-block;'>Auteur :</label>
<input type = 'text' id = 'Auteur' name = 'Auteur' value = ''>
</div>
<div style = 'margin-bottom: 10px;'>
<label for = 'Categorie' style = 'width: 100px; display: inline-block;'>Catégorie :</label>
<select id = 'Categorie' name = 'Categorie'>
<option value = ''>Toutes</option>
<option value = '1'>Location</option>
<option value = '2'>Recherche</option>
<option value = '3'>À vendre</option>
<option value = '4'>À donner</option>
<option value = '5'>Service offert</option>
<option value = '6'>Autre</option>
</select>
</div>
<div style = 'margin-bottom: 10px;'>
<label for = 'DateDebut' style = 'width: 100px; display: inline-block;'>Date :</label>
<input type = 'date' id = 'DateDebut' name = 'DateDebut'>
<span>à</span>
<input type = 'date' id = 'DateFin' name = 'DateFin'>
</div>
</div>

<script>

function toggleRechercheAvance() {
    var rechercheAvance = document.getElementById( 'divRechercheAvancé' );
    if ( rechercheAvance.style.display === 'none' ) {
        rechercheAvance.style.display = 'block';
    } else {
        rechercheAvance.style.display = 'none';
    }
}
</script>
<div id = 'divListe'>

<?php
if ( $result->num_rows > 0 ) {
    // Compteur pour le numéro séquentiel
    $sequentialNumber = 1 + $offset;
    // Commencer à partir de l'offset

        // Affichage des annonces
        while ($row = $result->fetch_assoc()) {
            $datePublication = date('Y-m-d H:i', strtotime($row['Parution']));
            $photoUrl = !empty($row['Photo']) ? $row['Photo'] : 'default.jpg'; // Par défaut si pas de photo
            ?>
            <div id="divAnnonce-<?php echo $row['NoAnnonce']; ?>" class="annonce">
                <div class="annonce-header">
                    <div class="text-left annonce-number"><?php echo $sequentialNumber++; ?></div>
                    <div class="text-right annonce-category"><?php echo getCategoryName($row['Categorie']); ?></div>
                </div>
                <div class="annonce-image">
                    <img alt="Image de <?php echo $row['DescriptionAbregee']; ?>" src="<?php echo $photoUrl; ?>">
                </div>
                <div class="annonce-body">
                    <h6 class="annonce-title">
                        <a href="Annonce.php?id=<?php echo $row['NoAnnonce']; ?>" class="ellipsis"><?php echo $row['DescriptionAbregee']; ?></a>
                    </h6>
                    <p class="non-gras ellipsis"><?php echo $row['DescriptionComplete']; ?></p>
                    <div class="text-right font-weight-bold">
                        <span>
                            <?php echo !empty($row['Prix']) ? number_format($row['Prix'], 2, '.', ' ') . " $" : 'N/A'; ?>
                        </span>
                    </div>
                </div>
                <div class="annonce-footer">
                    <div class="text-left footer-date" style="padding-left: 10px;"><?php echo $datePublication; ?></div>
                    <div class="text-right footer-number" style="padding-right: 10px;"><?php echo $row['NoAnnonce']; ?></div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>Aucune annonce trouvée.</p>";
    }
   
    ?>
</div>

<div id="divPages" class="pagination-container m-auto text-center">
    <a class="pagination-arrow" href="?page=1" style="color: black; font-weight: bold; text-decoration: none;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='black'">&lt;&lt;</a>
    <a class="pagination-arrow" href="?page=1" style="color: gray; font-weight: bolder; text-decoration: none;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='gray'">&lt;</a>

    <select id="ddlPage" class="pagination-select" onchange="location = this.value;">
        <option value="?page=1" selected="">1</option>
        <option value="?page=2">2</option>
        <option value="?page=3">3</option>
        <option value="?page=4">4</option>
        <option value="?page=5">5</option>
        <option value="?page=6">6</option>
        <option value="?page=7">7</option>
        <option value="?page=8">8</option>
        <option value="?page=9">9</option>
        <option value="?page=10">10</option>
    </select>

    <a class="pagination-arrow" href="?page=2" style="color: gray; font-weight: bold; text-decoration: none;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='gray'">&gt;</a>
    <a class="pagination-arrow" href="?page=10" style="color: black; font-weight: bolder; text-decoration: none;" onmouseover="this.style.color='blue'" onmouseout="this.style.color='black'">&gt;
    &gt;
    </a>
    </div>

    </body>
    </html>