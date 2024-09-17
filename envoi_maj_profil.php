<?php
require_once 'db.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    echo "Vous devez être connecté pour mettre à jour votre profil.";
    exit();
}

$userId = $_SESSION['NoUtilisateur'];
$message = ""; // Initialiser le message

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et valider les données
    $email = filter_var(trim($_POST['tbEmail']), FILTER_SANITIZE_EMAIL);
    $nom = trim($_POST['tbNom']);
    $prenom = trim($_POST['tbPrenom']);
    $noEmp = trim($_POST['tbNoEmp']);
    $posteTelBureau = trim($_POST['tbTelTPoste']);
    $telMaison = trim($_POST['tbTelM']);
    $telCellulaire = trim($_POST['tbTelC']);
    $statut = trim($_POST['tbStatut']);

    // Vérification des informations requises
    if (empty($nom) || empty($prenom) || empty($email) || empty($statut)) {
        $message = "<div style='color: red;'>Tous les champs requis doivent être remplis.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div style='color: red;'>Email invalide.</div>";
    } else {
        // Requête de mise à jour
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
            $stmt->bind_param('sssssssi', $nom, $prenom, $email, $telMaison, $telCellulaire, $posteTelBureau, $statut, $userId);
            
            if ($stmt->execute()) {
                $message = "<div style='color: green;'>Profil mis à jour avec succès.</div>";
                // Rediriger après une mise à jour réussie
                header("Location: modifier_profil.php?message=" . urlencode($message));
                exit();
            } else {
                $message = "<div style='color: red;'>Erreur lors de la mise à jour du profil : " . $stmt->error . "</div>";
            }
            
            $stmt->close();
        } else {
            $message = "<div style='color: red;'>Erreur lors de la préparation de la requête : " . $conn->error . "</div>";
        }
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
        <a href="logout.php" class="nav-item">Déconnexion</a>
    </nav>

    <div>
        <!-- Affichage du message de succès ou d'erreur -->
        <?php if (!empty($message)) echo $message; ?>
    </div>
</body>
</html>