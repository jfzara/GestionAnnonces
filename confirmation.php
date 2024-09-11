<?php
session_start();
include('config.php');

$token = $_GET['token'];

if ($token) {
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Token = ? AND Statut = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mise à jour du statut de l'utilisateur
        $updateStmt = $conn->prepare("UPDATE utilisateurs SET Statut = 1, Token = NULL WHERE Token = ?");
        $updateStmt->bind_param("s", $token);
        $updateStmt->execute();
        $_SESSION['success'] = "Votre compte a été confirmé. Vous pouvez maintenant vous connecter.";
        header('Location: login.php');
    } else {
        $_SESSION['error'] = "Lien de confirmation invalide ou déjà utilisé.";
        header('Location: enregistrement.php');
    }
} else {
    $_SESSION['error'] = "Aucun token trouvé.";
    header('Location: enregistrement.php');
}
?>