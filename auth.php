<?php
session_start();

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Rediriger vers la page de connexion si non connecté
        exit();
    }
}

// Gérer la déconnexion
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy(); // Détruire la session
    header("Location: login.php"); // Rediriger vers la page de connexion
    exit();
}
?>