<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur
$password = ""; // Remplacez par votre mot de passe
$dbname = "gestionannonces"; // Remplacez par le nom de votre base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Vérifiez si le token est passé dans l'URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Préparez la requête pour vérifier le token
    $query = 'SELECT NoUtilisateur FROM utilisateurs WHERE Token = ?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifiez si le token existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $noUtilisateur = $user['NoUtilisateur'];

        // Mettez à jour le statut de l'utilisateur à confirmé (9)
        $updateQuery = 'UPDATE utilisateurs SET Statut = 9, Token = NULL WHERE NoUtilisateur = ?';
        $stmtUpdate = $conn->prepare($updateQuery);
        $stmtUpdate->bind_param("i", $noUtilisateur);
        
        if ($stmtUpdate->execute()) {
            echo "Votre compte a été confirmé avec succès! Vous pouvez maintenant vous connecter.";
            // Redirection vers la page de connexion après confirmation
            header("Location: login.php"); // Remplacez par le chemin vers votre page de connexion
            exit();
        } else {
            echo "Erreur lors de la mise à jour du statut: " . $stmtUpdate->error;
        }
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