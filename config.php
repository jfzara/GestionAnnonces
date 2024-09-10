<?php
// config.php

define('SITE_NAME', 'Gestion Annonces'); // Définir le nom du site

// Paramètres de connexion à la base de données
$servername = "localhost"; // généralement localhost
$username = "root"; // Remplacez par votre nom d'utilisateur de base de données
$password = ""; // Remplacez par votre mot de passe de base de données
$dbname = "gestionannonces"; // Remplacez par le nom de votre base de données

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>