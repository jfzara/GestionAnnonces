<?php
session_start(); // Assurez-vous d'inclure session_start() ici

include 'config.php'; 
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['motdepasse'])) {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $motDePasse = $_POST['motdepasse'];

    // Requête SQL pour vérifier les informations d'identification
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($motDePasse, $user['MotDePasse'])) {
            $_SESSION['user_id'] = $user['NoUtilisateur'];

            // Enregistrement de la connexion
            $noUtilisateur = $user['NoUtilisateur'];
            $dateConnexion = date('Y-m-d H:i:s');
            $insertQuery = "INSERT INTO connexions (NoUtilisateur, Connexion) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("is", $noUtilisateur, $dateConnexion);
            $insertStmt->execute();

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Mot de passe incorrect."; 
        }
    } else {
        $error = "Adresse courriel non trouvée."; 
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
    <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
    <form method="POST" action="">
        <label for="email">Courriel :</label>
        <input type="email" id="email" name="email" required>
        
        <label for="motdepasse">Mot de passe :</label>
        <input type="password" id="motdepasse" name="motdepasse" required>
        
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>