<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    echo "<div style='text-align: center; color: red;'>Veuillez vous connecter.</div>";
    exit;
}

// Récupérer l'ID de l'utilisateur connecté et son rôle
$userId = $_SESSION['NoUtilisateur'];
$isAdmin = $_SESSION['isAdmin'] ?? false; // Supposons qu'on ait 'isAdmin' dans la session (1 pour admin, 0 pour non-admin)

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestionannonces');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Préparer la requête pour obtenir les annonces
if ($isAdmin) {
    // Si l'utilisateur est admin, récupérer toutes les annonces
    $stmt = $conn->prepare("SELECT * FROM annonces");
} else {
    // Sinon, récupérer uniquement les annonces de l'utilisateur connecté
    $stmt = $conn->prepare("SELECT * FROM annonces WHERE NoUtilisateur = ?");
    $stmt->bind_param("i", $userId);
}

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
    <style>
        /* Espacement des colonnes et styles généraux du tableau */
        table {
            width: 100%; /* Le tableau prend toute la largeur de l'écran */
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px; /* Padding de 10px pour les cellules */
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        td {
            white-space: nowrap; /* Pour éviter que les cellules soient compressées */
        }
        img {
            max-width: 100%;
            height: auto;
        }
        #boutonDesactiver {
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    transition: background-color 0.3s;
    cursor: pointer;
}

#boutonDesactiver:hover {
    background-color: #707070; /* Couleur de survol */
}

#boutonDesactiver.active {
    background-color: blue; /* Couleur lorsque le bouton est activé */
}

#boutonDesactiver.disabled {
    background-color: grey; /* Couleur par défaut */
}
       
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="annonces.php">Annonces</a>
        <a href="gestion_annonces.php">Gestion de vos annonces</a>
        <a href="modifier_profil.php">Modification du profil</a>
        <a href="logout.php">Déconnexion</a>
    </nav>

    <div class="text-right">
        <a href="ajouter_annonce.php" class="btn btn-success"  >Ajouter une annonce</a>
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
echo "<td><button style='background-color: #4caf50; color: white; border: none; padding: 10px; cursor: pointer; transition: background-color 0.3s;' 
onmouseover=\"this.style.backgroundColor='#45a049'\" 
onmouseout=\"this.style.backgroundColor='#4caf50'\" 
onclick='modifyAnnouncement({$row['NoAnnonce']})'>Modifier</button></td>";

echo "<td><button style='background-color: #f44336; color: white; border: none; padding: 10px; cursor: pointer; transition: background-color 0.3s;' 
onmouseover=\"this.style.backgroundColor='#e53935'\" 
onmouseout=\"this.style.backgroundColor='#f44336'\" 
onclick='confirmWithdrawal({$row['NoAnnonce']})'>Retirer</button></td>";

echo "<td>
        <button id='boutonDesactiver' class='disabled' data-state='desactiver' onclick='toggleStatus(this)'>Désactiver</button>
      </td>";

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
        button.style.backgroundColor = '#007bff'; // Changer la couleur en bleu
        button.classList.remove('désactiver'); // Retire la classe grise
    } else {
        button.innerText = 'Désactiver';
        button.setAttribute('data-state', 'desactiver');
        button.style.backgroundColor = 'grey'; // Rétablir la couleur grise
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