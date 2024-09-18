<?php
session_start();
$error = '';
$success = '';

// Supposons que vous ayez une fonction pour obtenir l'utilisateur
// $user = getUserById($_SESSION['user_id']);
$user = [
    'id' => 1, // Remplacez ceci par l'ID réel de l'utilisateur
    'password' => password_hash('ancienMotDePasse', PASSWORD_DEFAULT) // Remplacez ceci par le mot de passe réel
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validation des champs
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Les champs suivants doivent être remplis : mot de passe actuel, nouveau mot de passe, et confirmation du mot de passe.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Les nouveaux mots de passe ne correspondent pas.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } else {
        // Vérification du mot de passe actuel
        if (password_verify($currentPassword, $user['password'])) {
            // Mettre à jour le mot de passe
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            // Remplacez cette fonction par la logique réelle pour mettre à jour le mot de passe dans la base de données
            // updateUserPassword($user['id'], $hashedPassword);

            // Pour cet exemple, nous allons juste simuler le succès
            $success = 'Votre mot de passe a été mis à jour avec succès.';
        } else {
            $error = 'Le mot de passe actuel est incorrect.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Mot de Passe</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<nav class="navbar">
    <a href="annonces.php" class="nav-item">Annonces</a>
    <a href="gestion_annonces.php" class="nav-item">Gestion de vos annonces</a>
    <a href="modifier_profil.php" class="nav-item">Modification du profil</a>
    <a href="logout.php" class="nav-item">Déconnexion</a>
</nav>

<div id="divModifierMdp" class="form-container">
    <h1 id="titreModifierMdp">Modifier votre mot de passe</h1>
    <br>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form id="formModifierMdp" action="modifier_mdp.php" method="POST">
        <div class="form-group row">
            <label for="currentPassword" class="col-4 col-form-label">Mot de passe actuel</label>
            <div class="col-6">
                <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Entrez votre mot de passe actuel" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="newPassword" class="col-4 col-form-label">Nouveau mot de passe</label>
            <div class="col-6">
                <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Entrez votre nouveau mot de passe" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="confirmPassword" class="col-4 col-form-label">Confirmer le mot de passe</label>
            <div class="col-6">
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirmez votre nouveau mot de passe" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour le mot de passe</button>
    </form>
</div>
</body>
</html>