<?php
// db.php
$servername = "localhost";  
$username = "root";  
$password = "";  
$dbname = "gestionannonces";  

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}

// Activer les erreurs pour le débogage
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Options supplémentaires pour une meilleure gestion des erreurs
$conn->set_charset("utf8"); // Assurez-vous que la connexion utilise UTF-8
?>