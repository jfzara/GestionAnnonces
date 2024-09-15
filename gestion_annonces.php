<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    echo "<tr><td colspan='11' class='text-center'>Veuillez vous connecter.</td></tr>";
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$userId = $_SESSION['NoUtilisateur'];

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestionannonces');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Préparer la requête pour obtenir toutes les annonces
$stmt = $conn->prepare("SELECT * FROM annonces");
// Si vous voulez seulement les annonces de l'utilisateur connecté, utilisez cette requête :
$stmt = $conn->prepare("SELECT * FROM annonces WHERE NoUtilisateur = ?"); // Décommenter si nécessaire
$stmt->bind_param("i", $userId); // Décommenter si nécessaire

// Exécuter la requête
$stmt->execute();
$result = $stmt->get_result();

// Vérifiez si des annonces sont trouvées
?>
<nav class="navbar">
        <div class="container">
            <div id="menu" class="navbar-collapse">
                <div class="navbar-nav">
                    <a href="annonces.php" class="nav-item">Annonces</a>
                    <a href="gestion_annonces.php" class="nav-item">Gestion de vos annonces</a>
                    <a href="miseAJourProfil.php" class="nav-item">Modification du profil</a>
                    <a href="Deconnexion.php" class="nav-item">Déconnexion</a>
                    <span class="user-email">(test@test.test)</span>
                </div>
            </div>
        </div>
    </nav>
<div class="divGestion">
    <div class="text-right mx-3 my-2">
        <a href="ajouter_annonce.php" class="btn btn-primary text-light">Ajouter</a>
    </div>

    <h2>Gestion des annonces</h2> <!-- Titre de la page -->

    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>No</th>
                <th>No Annonce</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Date de parution</th>
                <th>État</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Sortir les données de chaque ligne
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>
                            <div class='overflow-hidden text-right imageSize'>
                                <img alt='Image' src='{$row['Photo']}' width='144' class='m-auto'>
                            </div>
                          </td>";
                    echo "<td>{$row['NoAnnonce']}</td>";
                    echo "<td><a href='Annonce.php?id={$row['NoAnnonce']}'>{$row['DescriptionAbregee']}</a></td>";
                    echo "<td>{$row['Categorie']}</td>";
                    echo "<td>{$row['Prix']} $</td>";
                    echo "<td>{$row['Parution']}</td>";
                    echo "<td>{$row['Etat']}</td>";
                    echo "<td>
                            <button class='btn btn-success' onclick='modifyAnnouncement({$row['NoAnnonce']})'>Modification</button>
                          </td>";
                    echo "<td>
                            <button class='btn btn-danger' onclick='confirmWithdrawal({$row['NoAnnonce']})'>Retrait</button>
                          </td>";
                    echo "<td>
                            <button class='btn btn-secondary toggle-btn' data-state='desactiver' onclick='toggleStatus(this)'>Désactiver</button>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11' class='text-center'>Aucune annonce trouvée.</td></tr>";
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

    if (currentState === 'desactiver') {
        button.innerText = 'Activer';
        button.setAttribute('data-state', 'activer');
    } else {
        button.innerText = 'Désactiver';
        button.setAttribute('data-state', 'desactiver');
    }
}

function modifyAnnouncement(id) {
    // Rediriger vers la page de modification de l'annonce
    window.location.href = `modifier_annonce.php?id=${id}`;
}

function confirmWithdrawal(id) {
    // Afficher une boîte de dialogue de confirmation
    const confirmation = confirm("Retirer cette annonce définitivement ?");
    if (confirmation) {
        // Rediriger vers la page de confirmation de retrait
        window.location.href = `confirmation_retirer_annonce.php?id=${id}`;
    }
}
</script>