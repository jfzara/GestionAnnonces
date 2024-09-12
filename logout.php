<?php
// logout.php
include 'config.php'; // Inclure le fichier de configuration pour la connexion à la base de données
session_start(); // Démarrer la session

if (isset($_SESSION['user_id'])) {
    // Récupérer l'ID de l'utilisateur à partir de la session
    $user_id = $_SESSION['user_id'];

    // Récupérer la dernière connexion de cet utilisateur
    $stmt = $conn->prepare("SELECT NoConnexion FROM connexions WHERE NoUtilisateur = ? ORDER BY Connexion DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $no_connexion = $row['NoConnexion'];

        // Mettre à jour la table connexions avec la date et l'heure de déconnexion
        $stmt = $conn->prepare("UPDATE connexions SET Deconnexion = NOW() WHERE NoConnexion = ?");
        $stmt->bind_param("i", $no_connexion);
        $stmt->execute();
    }
}

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header("Location: login.php");
exit();
?>