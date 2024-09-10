<?php
session_start();

include('config.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Préparer la requête pour vérifier le token
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['Statut'] === '1') {
            $_SESSION['message'] = "Votre adresse courriel a déjà été confirmée.";
            header('Location: login.php');
            exit();
        }

        $stmt = $conn->prepare("UPDATE utilisateurs SET Statut = '1', Token = NULL WHERE Token = ?");
        $stmt->bind_param("s", $token);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Votre adresse courriel a été confirmée avec succès !";
            header('Location: login.php');
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la confirmation.";
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Token invalide.";
        header('Location: login.php');
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "Aucun token fourni.";
    header('Location: login.php');
    exit();
}
?>