<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérifier l'utilisateur dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Vérifier le mot de passe
        if (password_verify($password, $user['MotDePasse'])) {
            // Vérifier si l'utilisateur est confirmé
            if ($user['Statut'] === '1') {
                $_SESSION['user_id'] = $user['id']; // Assurez-vous que 'id' est la colonne de l'identifiant
                header('Location: dashboard.php'); // Rediriger vers le tableau de bord
                exit();
            } else {
                $_SESSION['error'] = "Votre adresse courriel n'est pas confirmée. Veuillez vérifier votre courriel.";
                header('Location: login.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Aucun utilisateur trouvé avec cet e-mail.";
        header('Location: login.php');
        exit();
    }
}
?>