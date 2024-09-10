<?php
session_start();

// Inclure le fichier de configuration pour la connexion à la base de données
include('config.php');

// Fonction pour vérifier si l'utilisateur est authentifié
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Rediriger vers la page de connexion si non connecté
        exit();
    }
}

// Gérer la déconnexion
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy(); // Détruire la session
    header("Location: login.php"); // Rediriger vers la page de connexion
    exit();
}

// Gérer l'enregistrement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inclure le fichier de configuration pour la connexion à la base de données
    include('config.php');

    // Récupérer les données du formulaire
    $courriel = trim($_POST['email1']);
    $mot_de_passe = $_POST['password1'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    // Vous pouvez également ajouter d'autres informations si nécessaire (tels que NoTelMaison, etc.)

    // Vérifier si l'adresse courriel existe déjà dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Cette adresse courriel est déjà utilisée. Veuillez en choisir une autre.";
        header('Location: enregistrement.php');
        exit();
    } else {
        // Hachage du mot de passe
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);

        // Générer un token unique pour la confirmation
        $token = bin2hex(random_bytes(16));

        // Insérer les données dans la base de données
        $stmt = $conn->prepare("INSERT INTO utilisateurs (Courriel, MotDePasse, Nom, Prenom, Statut, Token) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("sssss", $courriel, $hashed_password, $nom, $prenom, $token);
        
        if ($stmt->execute()) {
            // Enregistrement réussi, envoyer le courriel de confirmation
            $to = $courriel;
            $subject = "Confirmation de votre inscription";
            $message = "Bonjour $nom $prenom,\n\n";
            $message .= "Merci de vous être inscrit ! Veuillez confirmer votre adresse courriel en cliquant sur le lien ci-dessous :\n";
            $message .= "http://votre_domaine/confirmation.php?token=$token\n\n";
            $message .= "Si vous n'avez pas créé ce compte, ignorez ce courriel.\n";
            
            // Envoyer le courriel
            mail($to, $subject, $message);

            $_SESSION['success'] = "Enregistrement réussi. Veuillez vérifier votre courriel pour confirmer votre inscription.";
            header('Location: dashboard.php'); 
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement. Veuillez réessayer.";
            header('Location: enregistrement.php');
        }
    }

    $stmt->close();
    $conn->close();
}