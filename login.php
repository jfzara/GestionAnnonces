<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include('config.php');

// Fonction pour vérifier si l'utilisateur est authentifié
function checkAuth() {
    if (isset($_SESSION['NoUtilisateur'])) {
        header("Location: dashboard.php"); // Rediriger vers le tableau de bord si déjà connecté
        exit();
    }
}

checkAuth();

// Gérer la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $courriel = trim($_POST['courriel']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmation_mot_de_passe = $_POST['confirmation_mot_de_passe'];

    // Vérification si les mots de passe correspondent
    if ($mot_de_passe !== $confirmation_mot_de_passe) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'utilisateur existe dans la base de données
        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
        $stmt->bind_param("s", $courriel);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Vérifier le mot de passe
            if (password_verify($mot_de_passe, $user['MotDePasse'])) {
                // Incrémenter le nombre de connexions
                $stmt = $conn->prepare("UPDATE utilisateurs SET NbConnexions = NbConnexions + 1 WHERE NoUtilisateur = ?");
                $stmt->bind_param("i", $user['NoUtilisateur']);
                $stmt->execute();

                // Enregistrer la connexion dans la table connexions
                $stmt = $conn->prepare("INSERT INTO connexions (NoUtilisateur) VALUES (?)");
                $stmt->bind_param("i", $user['NoUtilisateur']);
                $stmt->execute();

                // Récupérer l'ID de la connexion pour l'utiliser lors de la déconnexion
                $NoConnexion = $conn->insert_id;
                $_SESSION['NoConnexion'] = $NoConnexion; // Stocker l'ID de la connexion dans la session

                // Enregistrer l'ID de l'utilisateur dans la session
                $_SESSION['NoUtilisateur'] = $user['NoUtilisateur'];
                header('Location: dashboard.php'); // Rediriger vers le tableau de bord
                exit();
            } else {
                $_SESSION['error'] = "Mot de passe incorrect.";
            }
        } else {
            $_SESSION['error'] = "Aucun utilisateur trouvé avec cet e-mail.";
        }
    }
}

// Affichage des messages d'erreur
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function validateForm() {
            const email = document.getElementById('courriel').value;
            const password = document.getElementById('mot_de_passe').value;
            const confirmPassword = document.getElementById('confirmation_mot_de_passe').value;
            let errorMessage = "";

            // Validation de l'adresse courriel
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                errorMessage += "Veuillez entrer une adresse e-mail valide.<br>";
            }

            // Validation du mot de passe
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{5,15}$/;
            if (!passwordPattern.test(password)) {
                errorMessage += "Le mot de passe doit comporter entre 5 et 15 caractères, inclure des lettres (majuscules et minuscules) et des chiffres.<br>";
            }

            // Vérification si les mots de passe correspondent
            if (password !== confirmPassword) {
                errorMessage += "Les mots de passe ne correspondent pas.<br>";
            }

            // Afficher les erreurs si elles existent
            const errorDiv = document.getElementById('errorMessages');
            if (errorMessage) {
                errorDiv.innerHTML = errorMessage;
                return false; // Ne pas soumettre le formulaire
            }

            errorDiv.innerHTML = ""; // Effacer les messages d'erreur
            return true; // Si tout est valide, soumettre le formulaire
        }
    </script>
</head>
<body>
    

    <div id="errorMessages" style="color:red;">
        <?php
        // Affichage des messages d'erreur
        if (isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }
        ?>
    </div>

    <form action="login.php" method="POST" onsubmit="return validateForm();">
    <p class="titre connexion">Connexion</p>
        <label for="courriel">Courriel :</label>
        <input type="email" name="courriel" id="courriel" required>
        <br>
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required>
        <br>
        <br>
        <input class = "soumettre" type="submit" value="Se connecter">
        <p><a class= "lien" href="enregistrement.php">Créer un compte</a></p>
    </form>
    
    
</body>
</html>