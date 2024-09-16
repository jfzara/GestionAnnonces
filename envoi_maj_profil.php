<?php
require_once 'db.php';
session_start();

var_dump($_SESSION); // Déboguer les sessions

if (!isset($_SESSION['NoUtilisateur'])) {
    echo "Vous devez être connecté pour mettre à jour votre profil.";
    exit();
}

$userId = $_SESSION['NoUtilisateur'];

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['tbEmail'];
    $nom = $_POST['tbNom'];
    $prenom = $_POST['tbPrenom'];
    $noEmp = $_POST['tbNoEmp'];
    $posteTelBureau = $_POST['tbTelTPoste'];
    $telMaison = $_POST['tbTelM'];
    $telCellulaire = $_POST['tbTelC'];
    $statut = $_POST['tbStatut'];

    var_dump($nom, $prenom, $email, $telMaison, $telCellulaire, $posteTelBureau, $statut, $userId); // Déboguer les données

    $query = 'UPDATE utilisateurs SET
        Nom = ?,
        Prenom = ?,
        Courriel = ?,         
        NoTelMaison = ?,
        NoTelCellulaire = ?,
        NoTelTravail = ?,
        Statut = ?
        WHERE NoUtilisateur = ?';

    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('sssssisi', $nom, $prenom, $email, $telMaison, $telCellulaire, $posteTelBureau, $statut, $userId);
        
        if ($stmt->execute()) {
            echo "Profil mis à jour avec succès.";
            echo "Lignes affectées : " . $stmt->affected_rows;
        } else {
            echo "Erreur lors de la mise à jour du profil : " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }
}

$conn->close();
?>