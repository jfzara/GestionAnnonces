<?php

// Définition des variables de connexion
$servername = "localhost"; 
$username = "root";         
$password = "";             
$dbname = "gestionannonces"; 

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
} else {
    echo "Connexion réussie à la base de données.<br>";
}

// Vérifiez si le courriel est passé dans l'URL
if (isset($_GET['courriel'])) {
    $courriel = $_GET['courriel'];
    echo "Courriel : " . $courriel . "<br>";

    // Génération d'un nouveau token
    $token = bin2hex(random_bytes(16));
    echo "Token généré : " . $token . "<br>";

    // Insérer l'utilisateur avec le token
    $insertQuery = 'INSERT INTO utilisateurs (Courriel, Token) VALUES (?, ?)';
    $stmtInsert = $conn->prepare($insertQuery);
    $stmtInsert->bind_param("ss", $courriel, $token);

    if ($stmtInsert->execute()) {
        echo "Token inséré avec succès.<br>";
    } else {
        echo "Erreur lors de l'insertion du token : " . $stmtInsert->error . "<br>";
    }
}

// Affichez tous les utilisateurs pour débogage
$query = 'SELECT * FROM utilisateurs';
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "Utilisateur ID: " . $row['NoUtilisateur'] . " - Token: " . $row['Token'] . "<br>";
    }
} else {
    echo "Erreur lors de la récupération des utilisateurs : " . $conn->error . "<br>";
}

// Vérifiez si le token est passé dans l'URL pour la confirmation
if (isset($_GET['token'])) {
    $tokenToVerify = $_GET['token'];
    echo "Token à vérifier : " . $tokenToVerify . "<br>";

    // Préparez la requête pour vérifier le token
    $query = 'SELECT NoUtilisateur FROM utilisateurs WHERE Token = ?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tokenToVerify);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifiez si le token existe
    if ($result->num_rows > 0) {
        // Token trouvé, procéder à la confirmation
        $user = $result->fetch_assoc();
        $noUtilisateur = $user['NoUtilisateur'];

        // Mettez à jour le statut de l'utilisateur à confirmé
        $updateQuery = 'UPDATE utilisateurs SET Statut = 9, Token = NULL WHERE NoUtilisateur = ?';
        $stmtUpdate = $conn->prepare($updateQuery);
        $stmtUpdate->bind_param("i", $noUtilisateur);
        
        if ($stmtUpdate->execute()) {
            echo "Votre compte a été confirmé avec succès! Vous pouvez maintenant vous connecter.";
            header("Location: login.php");
            exit();
        } else {
            echo "Erreur lors de la mise à jour du statut: " . $stmtUpdate->error . "<br>";
        }
    } else {
        echo "Token invalide ou expiré.<br>";
    }
} else {
    echo "Aucun token fourni.<br>";
}

// Fermez les déclarations si elles ont été initialisées
if (isset($stmtInsert)) {
    $stmtInsert->close();
}
if (isset($stmt)) {
    $stmt->close();
}
if (isset($stmtUpdate)) {
    $stmtUpdate->close();
}

// Fermez la connexion
$conn->close();
?>