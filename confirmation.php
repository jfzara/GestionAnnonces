<?php


// Définition des variables de connexion
$servername = "localhost"; // ou votre serveur
$username = "root";         // remplacez par votre nom d'utilisateur
$password = "";             // remplacez par votre mot de passe
$dbname = "gestionannonces"; // remplacez par le nom de votre base de données

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Génération d'un nouveau token
$token = bin2hex(random_bytes(16));

// Insérer l'utilisateur avec le token
$insertQuery = 'INSERT INTO utilisateurs (Token) VALUES (?)';
$stmtInsert = $conn->prepare($insertQuery);
$stmtInsert->bind_param("s", $token);
$stmtInsert->execute();

// Vérifiez si le token est inséré
echo "Token inséré : " . $token;

// Vérifiez si le token est passé dans l'URL pour la confirmation
if (isset($_GET['token'])) {
    $tokenToVerify = $_GET['token'];

    // Préparez la requête pour vérifier le token
    $query = 'SELECT NoUtilisateur FROM utilisateurs WHERE Token = ?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tokenToVerify);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifiez si le token existe
    if ($result->num_rows > 0) {
        // Token trouvé, procéder à la confirmation
        echo "Token valide.";
    } else {
        echo "Token invalide ou expiré.";
    }
} else {
    echo "Aucun token fourni.";
}

// Fermez la connexion
$stmt->close();
$conn->close();
?>