<?php
// connexion à la base de données
require 'db.php'; // Remplacez par le chemin vers votre fichier de connexion

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Assurez-vous de valider l'ID

    // Supprimer l'annonce de la base de données
    $query = "DELETE FROM annonces WHERE NoAnnonce = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "L'annonce a été retirée avec succès.";
    } else {
        echo "Erreur lors du retrait de l'annonce : " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Aucun ID d'annonce fourni.";
}

$conn->close();
?>