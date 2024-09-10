<?php
// hash_password.php
include 'config.php'; // Inclure le fichier de configuration
include 'db.php'; // Inclure le fichier de connexion à la base de données

$noUtilisateur = 2; // L'ID de l'utilisateur dont vous voulez mettre à jour le mot de passe
$nouveauMotDePasse = "NouveauMdp123"; // Le mot de passe en clair

// Hachage du mot de passe
$hashedPassword = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);

// Mise à jour du mot de passe dans la base de données
$stmt = $conn->prepare("UPDATE utilisateurs SET MotDePasse = ? WHERE NoUtilisateur = ?");
$stmt->bind_param("si", $hashedPassword, $noUtilisateur);

if ($stmt->execute()) {
    echo "Le mot de passe a été mis à jour avec succès.";
} else {
    echo "Erreur lors de la mise à jour du mot de passe : " . $stmt->error;
}
?>