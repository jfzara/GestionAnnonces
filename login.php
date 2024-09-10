<?php
// login.php
include 'config.php'; // Inclure le fichier de configuration
include 'db.php'; // Inclure le fichier de connexion à la base de données

$error = ''; // Initialiser la variable d'erreur

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['motdepasse'])) {
    $email = $_POST['email'];
    $motDePasse = $_POST['motdepasse'];

    // Requête SQL pour vérifier les informations d'identification
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($motDePasse, $user['MotDePasse'])) {
            // Mot de passe correct, connexion réussie
            $_SESSION['user_id'] = $user['NoUtilisateur']; // Enregistrer l'ID de l'utilisateur dans la session
            
            // Enregistrer la connexion dans la table connexions
            $noUtilisateur = $user['NoUtilisateur'];
            $dateConnexion = date('Y-m-d H:i:s'); // Date et heure actuelles
            $insertQuery = "INSERT INTO connexions (NoUtilisateur, Connexion) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("is", $noUtilisateur, $dateConnexion);
            $insertStmt->execute();

            header("Location: dashboard.php"); // Rediriger vers le tableau de bord
            exit();
        } else {
            $error = "Identifiants incorrects."; // Message d'erreur
        }
    } else {
        $error = "Identifiants incorrects."; // Message d'erreur
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Connexion</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
    <h1>Connexion</h1>
    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?> <!-- Afficher le message d'erreur -->
    <form method="POST" action="">
        <label for="email">Courriel :</label>
        <input type="email" id="email" name="email" required>
        
        <label for="motdepasse">Mot de passe :</label>
        <input type="password" id="motdepasse" name="motdepasse" required>
        
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>