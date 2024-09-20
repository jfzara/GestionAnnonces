<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include('config.php');

// Fonction pour vérifier si l'utilisateur est authentifié
function checkAuth() {
    if (isset($_SESSION['NoUtilisateur'])) {
        header("Location: annonces.php"); // Rediriger vers annonces.php si déjà connecté
        exit();
    }
}

checkAuth();

// Gérer la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $courriel = trim($_POST['courriel']);
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier si l'utilisateur existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Vérifier le mot de passe
        if (!is_null($user['MotDePasse']) && password_verify($mot_de_passe, $user['MotDePasse'])) {
            // Incrémenter le nombre de connexions
            $stmt = $conn->prepare("UPDATE utilisateurs SET NbConnexions = NbConnexions + 1 WHERE NoUtilisateur = ?");
            $stmt->bind_param("i", $user['NoUtilisateur']);
            $stmt->execute();

            // Enregistrer la connexion dans la table connexions
            $stmt = $conn->prepare("INSERT INTO connexions (NoUtilisateur) VALUES (?)");
            $stmt->bind_param("i", $user['NoUtilisateur']);
            $stmt->execute();

            // Récupérer l'ID de la connexion
            $_SESSION['NoConnexion'] = $conn->insert_id; // Stocker l'ID de la connexion dans la session

            // Enregistrer l'ID de l'utilisateur dans la session
            $_SESSION['NoUtilisateur'] = $user['NoUtilisateur'];

            // Déterminer si l'utilisateur est un administrateur
            $_SESSION['isAdmin'] = ($courriel === 'admin@gmail.com'); // Vérification simplifiée

            // Rediriger vers annonces.php
            header('Location: annonces.php');
            exit();
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
        }
    } else {
        $_SESSION['error'] = "Aucun utilisateur trouvé avec cet e-mail.";
    }
}

// Affichage des messages d'erreur
if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
    
    <script>


        function checkAdminEmail() {
            const email = document.getElementById('courriel').value;
            const adminSpan = document.getElementById('adminIndicator');

            // Vérifier si l'email correspond à admin@gmail.com
            if (email === "admin@gmail.com") {
                adminIndicator.style = "display: inline-block; font-weight: bold; padding: 2px; margin: 0; width: fit-content; color: green; background-color: white; border-radius: 4px;";// Faire apparaître l'indicateur
            }  
        }
  

        function validateForm() {
            const email = document.getElementById('courriel').value;
            const password = document.getElementById('mot_de_passe').value;
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
 
    <form class="loginform" action="login.php" method="POST" onsubmit="return validateForm();">
        <p class="titre connexion">Connexion</p>
        <label for="courriel">Courriel :</label> 
        <input type="email" name="courriel" id="courriel" required oninput="checkAdminEmail();">
        <span id="adminIndicator" style= "display: none;">  ADMIN </span>
        <br>
        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required>
        <br>
        <p><a href="mot_de_passe_oublie.php">Mot de passe oublié ?</a></p>
        <br>
        <input class="soumettre" type="submit" value="Se connecter">

        <div id="errorMessages" style="color:red;">
            <?php
            // Affichage des messages d'erreur
            if (isset($error_message)) {
                echo $error_message;
            }
            ?>
        </div>
        <p><a class="lien_enregistrement" href="enregistrement.php">Créer un compte</a></p>
    </form>
    
</body>
</html>