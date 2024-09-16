<?php
session_start(); // Démarre une nouvelle session ou reprend une session existante

// Inclure le fichier de connexion à la base de données
include 'db.php';

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    // Redirection vers la page de connexion ou une autre page
    header('Location: annonces.php'); // Vous pouvez changer cette ligne si nécessaire
    exit();
}

// Gestion des erreurs
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Valider les champs
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Tous les champs sont obligatoires.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Les nouveaux mots de passe ne correspondent pas.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
    } else {
        // Vérifier le mot de passe actuel
        $userId = $_SESSION['NoUtilisateur']; // Utilisation de NoUtilisateur
        $stmt = $conn->prepare("SELECT MotDePasse FROM utilisateurs WHERE NoUtilisateur = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashedPassword);
            $stmt->fetch();

            // Vérifiez si le mot de passe actuel est correct
            if (password_verify($currentPassword, $hashedPassword)) {
                // Mise à jour du mot de passe
                $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE utilisateurs SET MotDePasse = ? WHERE NoUtilisateur = ?");
                $updateStmt->bind_param("si", $newHashedPassword, $userId);

                if ($updateStmt->execute()) {
                    $success = 'Mot de passe mis à jour avec succès.';
                } else {
                    $error = 'Une erreur s\'est produite lors de la mise à jour du mot de passe. Veuillez réessayer.';
                }
            } else {
                $error = 'Le mot de passe actuel est incorrect. Veuillez réessayer.';
            }
        } else {
            $error = 'Utilisateur non trouvé. Veuillez vous reconnecter.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le mot de passe</title>
    <link rel="stylesheet" href="styles.css"> <!-- Incluez votre fichier CSS -->
</head>
<body>

<div class="form-container">
    <h1>Modifier le mot de passe</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="modifier_mdp.php" method="POST">
        <div class="form-group">
            <label for="currentPassword">Mot de passe actuel</label>
            <input type="password" id="currentPassword" name="currentPassword" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="newPassword">Nouveau mot de passe</label>
            <input type="password" id="newPassword" name="newPassword" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="confirmPassword">Confirmer le nouveau mot de passe</label>
            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour le mot de passe</button>
        <a href="annonces.php" class="btn btn-info">Retour à l'accueil</a>
    </form>
    
</div>

</body>
</html>