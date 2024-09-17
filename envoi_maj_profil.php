<?php
// Inclure le fichier de connexion à la base de données
include('db.php');

$message = ""; // Initialiser le message

// Vérifier si les données ont été envoyées
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et nettoyage des données du formulaire
    $nom = isset($_POST['tbNom']) ? trim($_POST['tbNom']) : '';
    $prenom = isset($_POST['tbPrenom']) ? trim($_POST['tbPrenom']) : '';
    $email = isset($_POST['tbEmail']) ? filter_var(trim($_POST['tbEmail']), FILTER_SANITIZE_EMAIL) : '';
    $telMaison = isset($_POST['tbTelM']) ? trim($_POST['tbTelM']) : '';
    $telCellulaire = isset($_POST['tbTelC']) ? trim($_POST['tbTelC']) : '';
    $posteBureau = isset($_POST['tbTelTPoste']) ? trim($_POST['tbTelTPoste']) : '';
    $noEmp = isset($_POST['tbNoEmpl']) ? trim($_POST['tbNoEmpl']) : '';
    $statut = isset($_POST['tbStatut']) ? trim($_POST['tbStatut']) : '';

    // Validation des données
    if (empty($noEmpl) || !is_numeric($noEmpl)) {
        $message = "<div style='color: red;'>Erreur : Le numéro d'employé doit être un nombre entier.</div>";
    } else {
        // Préparer la requête SQL
        $sql = "UPDATE utilisateurs SET 
            Nom = ?, 
            Prenom = ?, 
            Courriel = ?, 
            NoTelMaison = ?, 
            NoTelCellulaire = ?, 
            NoTelTravail = ?, 
            Statut = ? 
        WHERE NoEmpl = ?";
var_dump($noEmpl);
        // Préparer la requête
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Erreur lors de la préparation de la requête: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("sssssssi", $nom, $prenom, $email, $telMaison, $telCellulaire, $posteBureau, $statut, $noEmp);

        // Exécuter la requête
        if ($stmt->execute()) {
            $message = "Mise à jour réussie!";
        } else {
            $message = "<div style='color: red;'>Erreur lors de la mise à jour: " . $stmt->error . "</div>";
        }

        // Fermer la déclaration
        $stmt->close();
    }
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour de Profil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="login.php" class="nav-item">Se connecter</a>
    </nav>

    <div>
        <!-- Affichage du message de succès ou d'erreur -->
        <?php if (!empty($message)) echo $message; ?>
    </div>
</body>
</html>