<?php
session_start();
include 'db.php'; // Inclusion du fichier de connexion à la base de données

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

        // Vérifiez si l'utilisateur est déjà confirmé
        if ($user['Statut'] == 9) {
            echo "Votre compte est déjà confirmé.";
        } else {
            // Mettez à jour le statut de l'utilisateur à 9 (Confirmé)
            $updateStatutQuery = 'UPDATE utilisateurs SET Statut = 9 WHERE Token = ?';
            $stmtUpdateStatut = $conn->prepare($updateStatutQuery);
            $stmtUpdateStatut->bind_param("s", $token);

            if ($stmtUpdateStatut->execute()) {
                // Compte confirmé avec succès, redirection vers la page de login
                echo "Votre compte a été confirmé avec succès. Vous allez être redirigé vers la page de connexion.";
                header("Refresh: 3; url=login.php"); // Redirection après 3 secondes
                exit(); // Stopper l'exécution après la redirection
            } else {
                // Affiche un message générique d'erreur
                echo "Une erreur est survenue lors de la confirmation du compte. Veuillez réessayer plus tard.";
            }

            $stmtUpdateStatut->close();
        }
    } else {
        // Token invalide ou déjà utilisé
        echo "Token invalide ou déjà utilisé.";
    }

    $stmt->close();
} else {
    echo "Aucun token fourni.";
}

$conn->close();
?>