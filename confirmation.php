<?php
session_start();
include('config.php'); // Inclure la configuration de la base de données

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifier si le token existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mettre à jour le statut de l'utilisateur à 9 (confirmé)
        $stmt = $conn->prepare("UPDATE utilisateurs SET Statut = 9 WHERE Token = ?");
        $stmt->bind_param("s", $token);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Votre compte a été confirmé avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la confirmation de votre compte.";
        }
    } else {
        $_SESSION['error'] = "Token invalide ou déjà utilisé.";
    }
} else {
    $_SESSION['error'] = "Aucun token fourni.";
}

$stmt->close();
$conn->close();

// Redirection vers la page de connexion
header('Location: login.php');
exit();
?>