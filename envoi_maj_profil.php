<?php
require_once 'db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    echo "Vous devez être connecté pour mettre à jour votre profil.";
    exit();
}

$userId = $_SESSION['NoUtilisateur'];

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['tbEmail'];
    $nom = $_POST['tbNom'];
    $prenom = $_POST['tbPrenom'];
    $noEmp = $_POST['tbNoEmp'];
    $posteTelBureau = $_POST['tbTelTPoste'];
    $telMaison = $_POST['tbTelM'];
    $telCellulaire = $_POST['tbTelC'];
    $statut = $_POST['tbStatut'];

    $query = 'UPDATE utilisateurs SET
        Nom = ?,
        Prenom = ?,
        Courriel = ?,         
        NoTelMaison = ?,
        NoTelCellulaire = ?,
        NoTelTravail = ?,
        Statut = ?
        WHERE NoUtilisateur = ?';

    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('sssssisi', $nom, $prenom, $email, $telMaison, $telCellulaire, $posteTelBureau, $statut, $userId);
        
        if ($stmt->execute()) {
            echo "<div style='color: green;'>Profil mis à jour avec succès.</div>";
        } else {
            echo "<div style='color: red;'>Erreur lors de la mise à jour du profil : " . $stmt->error . "</div>";
        }
        
        $stmt->close();
    } else {
        echo "<div style='color: red;'>Erreur lors de la préparation de la requête : " . $conn->error . "</div>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="styles.css">  
</head>
<body>
    <nav class="navbar">
        <a href="annonces.php" class="nav-item">Annonces</a>
        <a href="gestion_annonces.php" class="nav-item">Gestion de vos annonces</a>
        <a href="modifier_profil.php" class="nav-item">Modification du profil</a>
        <a href="Deconnexion.php" class="nav-item">Déconnexion</a>
    </nav>

    <!-- Votre contenu ici -->
    <div>
        <!-- Affichage du message de succès ou d'erreur -->
        <?php if (isset($message)) echo $message; ?>
    </div>

</body>
</html>