<?php
// Inclure le fichier de connexion à la base de données
include('db.php');

// Vérifier si les données ont été envoyées
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et nettoyer les données du formulaire
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telMaison = trim($_POST['NoTelMaison']);
    $telCellulaire = trim($_POST['NoTelCellulaire']);
    $posteBureau = trim($_POST['PosteBureau']);
    $statut = trim($_POST['statut']);
    $noEmp = trim($_POST['NoEmpl']);

    // Effectuer une validation supplémentaire ici
    // Exemple : vérifier si le numéro d'employé est valide
    if (empty($noEmp) || !is_numeric($noEmp)) {
        die("Erreur : Le numéro d'employé doit être un nombre entier.");
    }

    // Préparer la requête SQL
    $sql = "UPDATE utilisateurs SET 
        Nom = ?, 
        Prenom = ?, 
        Courriel = ?, 
        NoTelMaison = ?, 
        NoTelCellulaire = ?, 
        NoTelTravail = ?, 
        Statut = ? 
    WHERE NoEmpl = ?";

    // Préparer la requête
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur lors de la préparation de la requête: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssssi", $nom, $prenom, $email, $telMaison, $telCellulaire, $posteBureau, $statut, $noEmp);

    // Exécuter la requête
    if ($stmt->execute()) {
        echo "Mise à jour réussie!";
    } else {
        echo "Erreur lors de la mise à jour: " . $stmt->error;
    }

    // Fermer la déclaration et la connexion
    $stmt->close();
    $conn->close();
} else {
    echo "Erreur : Méthode de requête invalide.";
}
?>