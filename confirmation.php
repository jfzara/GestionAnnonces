<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Vérifier le token dans l'URL
$token = $_GET['token'] ?? null;
if ($token) {
    // Configurations de base de données
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gestionannonces";

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Mettre à jour le statut de l'utilisateur (exemple : statut = 1)
    $queryUpdate = 'UPDATE utilisateurs SET Statut = 1 WHERE Token = ?'; // Assurez-vous que la colonne Statut existe
    $stmtUpdate = $conn->prepare($queryUpdate);
    $stmtUpdate->bind_param("s", $token);
    
    if ($stmtUpdate->execute()) {
        $_SESSION['message'] = "Votre compte a été confirmé avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la confirmation de votre compte.";
    }

    // Fermer la connexion à la base de données
    $conn->close();
    
    // Redirection vers la page de connexion
    header('Location: login.php');
    exit();
} else {
    echo "Token invalide.";
}
?>