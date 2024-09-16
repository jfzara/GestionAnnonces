<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    echo "<div style='text-align: center; color: red;'>Veuillez vous connecter.</div>";
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['NoUtilisateur'];

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestionannonces');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Préparer la requête pour obtenir les annonces de l'utilisateur connecté
$stmt = $conn->prepare("SELECT * FROM annonces WHERE NoUtilisateur = ?");
$stmt->bind_param("i", $userId);

// Exécuter la requête
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des annonces</title>
    <link rel="stylesheet" href="styles.css">  
</head>
<body>
    <nav class="navbar">
        <a href="annonces.php">Annonces</a>
        <a href="gestion_annonces.php">Gestion de vos annonces</a>
        <a href="modifier_profil.php">Modification du profil</a>
        <a href="logout.php">Déconnexion</a>
    </nav>

    <div class="text-right">
        <a href="ajouter_annonce.php" class="btn btn-success">Ajouter une annonce</a>
    </div>

    <h2>Gestion des annonces</h2>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>No Annonce</th>
                    <th>Description</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Date de parution</th>
                    <th>État</th>
                    <th colspan="3"></th>  <!-- Trois cellules vides pour les boutons -->
                </tr>
            </thead>
            <tbody>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            
            // Vérifier si l'image est définie, sinon utiliser une image par défaut
            $photoSrc = !empty($row['Photo']) ? $row['Photo'] : 'photos-annonces/default.jpg'; // Mettez le chemin de votre image par défaut ici
            echo "<td><img src='{$photoSrc}' alt='Image' width='144'></td>";
            
            echo "<td>{$row['NoAnnonce']}</td>";
            echo "<td><a href='Annonce.php?id={$row['NoAnnonce']}'>{$row['DescriptionAbregee']}</a></td>";
            echo "<td>{$row['Categorie']}</td>";
            echo "<td>{$row['Prix']} $</td>";
            echo "<td>{$row['Parution']}</td>";
            echo "<td>{$row['Etat']}</td>";
            // Chaque bouton dans sa propre cellule
            echo "<td><button class='btn btn-success' onclick='modifyAnnouncement({$row['NoAnnonce']})'>Modifier</button></td>";
            echo "<td><button class='btn btn-danger' onclick='confirmWithdrawal({$row['NoAnnonce']})'>Retirer</button></td>";
            echo "<td><button class='btn btn-secondary toggle-btn' data-state='desactiver' onclick='toggleStatus(this)'>Désactiver</button></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='10' class='text-center'>Aucune annonce trouvée.</td></tr>";
    }
    // Fermer la connexion
    $stmt->close();
    $conn->close();
    ?>
</tbody>
        </table>
    </div>

    <script>
    function toggleStatus(button) {
        const currentState = button.getAttribute('data-state');

        // Logique de changement de texte et d'état
        if (currentState === 'desactiver') {
            button.innerText = 'Activer';
            button.setAttribute('data-state', 'activer');
            button.classList.remove('désactiver'); // Retire la classe grise
        } else {
            button.innerText = 'Désactiver';
            button.setAttribute('data-state', 'desactiver');
            button.classList.add('désactiver'); // Ajoute la classe grise
        }
    }

    function modifyAnnouncement(id) {
        window.location.href = `modifier_annonce.php?id=${id}`;
    }

    function confirmWithdrawal(id) {
        const confirmation = confirm("Retirer cette annonce définitivement ?");
        if (confirmation) {
            window.location.href = `confirmation_retirer_annonce.php?id=${id}`;
        }
    }
    </script>
</body>
</html>