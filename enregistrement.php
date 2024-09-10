
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement</title>
</head>
<body>
    <h1>Inscription</h1>


<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include('config.php');

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = filter_var(trim($_POST['email1']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['password1'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);

    // Vérification de l'existence de l'email
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Cette adresse courriel est déjà utilisée.";
        header('Location: enregistrement.php');
        exit();
    } else {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        // Insérer les données dans la base de données
        $stmt = $conn->prepare("INSERT INTO utilisateurs (Courriel, MotDePasse, Nom, Prenom, Statut, Token) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("sssss", $courriel, $hashed_password, $nom, $prenom, $token);
        
        if ($stmt->execute()) {
            $to = $courriel;
            $subject = "Confirmation de votre inscription";
            $message = "Bonjour $nom $prenom,\n\nMerci de vous être inscrit ! Veuillez confirmer votre adresse courriel en cliquant sur le lien ci-dessous :\nhttp://votre_domaine/confirmation.php?token=$token\n\nSi vous n'avez pas créé ce compte, ignorez ce courriel.";
            
            mail($to, $subject, $message);

            $_SESSION['success'] = "Enregistrement réussi. Veuillez vérifier votre courriel pour confirmer votre inscription.";
            header('Location: dashboard.php'); 
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement.";
            header('Location: enregistrement.php');
            exit();
        }
    }
    $stmt->close();
    $conn->close();
}
?>
<form action="enregistrement.php" method="POST">
        <label for="email1">Courriel :</label>
        <input type="email" name="email1" id="email1" required>
        <br>
        <label for="password1">Mot de passe :</label>
        <input type="password" name="password1" id="password1" required>
        <br>
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required>
        <br>
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" required>
        <br>
        <input type="submit" value="S'enregistrer">
    </form>
</body>
</html>