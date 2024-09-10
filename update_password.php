<?php
require 'config.php'; // Connexion à la base de données

// Choisir un mot de passe qui respecte les contraintes de longueur
$nouveauMotDePasse = 'NouveauMdp'; // Longueur entre 5 et 15 caractères
$hashedPassword = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);

$email = 'nouvelutilisateur@example.com'; 
$updateStmt = $conn->prepare("UPDATE utilisateurs SET MotDePasse = ? WHERE Courriel = ?");
$updateStmt->bind_param("ss", $hashedPassword, $email);

if ($updateStmt->execute()) {
    echo "Mot de passe mis à jour avec succès.";
} else {
    echo "Erreur lors de la mise à jour du mot de passe : " . $conn->error;
}
?>