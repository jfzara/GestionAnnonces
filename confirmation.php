<?php
session_start();
include 'db.php'; // Incluez votre fichier de connexion à la base de données

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Préparez la requête pour récupérer l'utilisateur par token
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token valide
        $user = $result->fetch_assoc();
        echo "Token valide. Action confirmée.<br>";

        // Mettez à jour le statut de l'utilisateur à 9 (Confirmé)
        $updateStatutQuery = 'UPDATE utilisateurs SET Statut = 9 WHERE Token = ?';
        $stmtUpdateStatut = $conn->prepare($updateStatutQuery);
        $stmtUpdateStatut->bind_param("s", $token);

        if ($stmtUpdateStatut->execute()) {
            echo "Votre compte a été confirmé avec succès!";
        } else {
            echo "Erreur lors de la confirmation du compte: " . $stmtUpdateStatut->error;
        }

        $stmtUpdateStatut->close();
    } else {
        // Token invalide
        echo "Token invalide.";
    }

    $stmt->close();
} else {
    echo "Aucun token fourni.";
}

$conn->close();
?>