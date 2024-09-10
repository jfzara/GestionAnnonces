<?php
// db.php
$servername = "localhost"; // Votre serveur de base de données
$username = "root"; // Votre nom d'utilisateur pour la base de données
$password = ""; // Votre mot de passe pour la base de données
$dbname = "gestionannonces"; // Remplacez par le nom de votre base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
?>