<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Inclure le fichier de configuration pour la connexion à la base de données
include('config.php');

// Fonction pour vérifier si l'utilisateur est authentifié
function checkAuth() {
    if (isset($_SESSION['user_id'])) {
        header("Location: dashboard.php"); // Rediriger vers le tableau de bord si déjà connecté
        exit();
    }
}

// Appeler la fonction pour vérifier l'authentification
checkAuth();

// Gérer la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $courriel = trim($_POST['courriel']);
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier si l'utilisateur existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Vérifier le mot de passe
        if (password_verify($mot_de_passe, $user['MotDePasse'])) {
            $_SESSION['user_id'] = $user['ID']; // Enregistrer l'ID de l'utilisateur dans la session
            header('Location: dashboard.php'); // Rediriger vers le tableau de bord
            exit();
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
        }
    } else {
        $_SESSION['error'] = "Aucun utilisateur trouvé avec cet e-mail.";
    }
}

// Affichage des messages d'erreur
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>

    <?php
    // Affichage des messages d'erreur ou de succès
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red;'>".$_SESSION['error']."</p>";
        unset($_SESSION['error']);
    }
    ?>

    <form action="login.php" method="POST">
        <label for="courriel">Courriel :</label>
        <input type="email" name="courriel" id="courriel" required>
        <br>
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required>
        <br>
        <input type="submit" value="Se connecter">
    </form>

    <p><a href="enregistrement.php">Créer un compte</a></p>
</body>
</html>