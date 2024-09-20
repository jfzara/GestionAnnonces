<?php
session_start();
include('config.php');

// Vérifier si le token est présent dans l'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];
   
    // Vérifier si le token est valide et qu'il n'a pas expiré (par exemple, généré il y a moins d'une heure)
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE token = ? AND Modification > (NOW() - INTERVAL 1 HOUR)");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        

        // Si le formulaire de réinitialisation du mot de passe a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier que le nouveau mot de passe est fourni et valide
            if (isset($_POST['nouveau_mot_de_passe']) && !empty(trim($_POST['nouveau_mot_de_passe']))) {
                $nouveau_mot_de_passe = password_hash(trim($_POST['nouveau_mot_de_passe']), PASSWORD_BCRYPT);

                // Mettre à jour le mot de passe, invalider le token et réinitialiser la date de modification
                $stmt = $conn->prepare("UPDATE utilisateurs SET MotDePasse = ?, token = NULL, Modification = NOW() WHERE NoUtilisateur = ?");
                $stmt->bind_param("si", $nouveau_mot_de_passe, $user['NoUtilisateur']);
                $stmt->execute();

                // Vérifier si la mise à jour a réussi
                if ($stmt->affected_rows > 0) {
                    // Rediriger l'utilisateur vers la page de connexion après réinitialisation réussie
                    $_SESSION['success'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                    header("Location: login.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Erreur lors de la mise à jour du mot de passe. Veuillez réessayer.";
                }
            } else {
                $_SESSION['error'] = "Veuillez entrer un nouveau mot de passe.";
            }
        }
    } else {
        echo "Aucun utilisateur trouvé avec le token fourni.<br>"; // Afficher un message si aucun utilisateur n'est trouvé
        $_SESSION['error'] = "Le lien de réinitialisation est invalide ou a expiré.";
    }
} else {
    $_SESSION['error'] = "Aucun token fourni.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
</head>
<body>
    <?php if (isset($_SESSION['error'])): ?>
        <div style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <!-- Formulaire de réinitialisation de mot de passe -->
    <form action="reinitialiser_mot_de_passe.php?token=<?php echo htmlentities($_GET['token']); ?>" method="POST">
        <label for="nouveau_mot_de_passe">Nouveau mot de passe :</label>
        <input type="password" name="nouveau_mot_de_passe" id="nouveau_mot_de_passe" required>
        <input type="submit" value="Réinitialiser">
    </form>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
</body>
</html>