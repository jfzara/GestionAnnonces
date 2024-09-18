<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  

session_start(); // Démarrer la session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $motDePasse = $_POST['motDePasse'];

    // Stocker le mot de passe dans la session
    $_SESSION['motDePasse'] = $motDePasse;

    // Rediriger vers la page de modification du profil
    header("Location: modifier_profil.php");
    exit();
}

// Charger les variables d'environnement à partir du fichier .env et gérer les erreurs
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Le fichier .env est introuvable ou ne peut pas être lu : " . $e->getMessage());
}

include('config.php'); // Inclure la connexion à la base de données

// Initialisation des constantes pour les messages
const ERROR_EMAIL_USED = "Cette adresse courriel est déjà utilisée.";
const ERROR_PASSWORD_MISMATCH = "Les mots de passe ne correspondent pas.";
const ERROR_PASSWORD_INVALID = "Le mot de passe doit comporter entre 5 et 15 caractères, inclure des lettres (majuscules et minuscules) et des chiffres.";
const ERROR_EMPTY_FIELDS = "Veuillez remplir tous les champs obligatoires.";
const ERROR_REGISTRATION = "Erreur lors de l'enregistrement.";
const SUCCESS_REGISTRATION = "Enregistrement réussi. Veuillez vérifier votre courriel pour confirmer votre inscription.";

// Vérifier si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialisation des messages d'erreur
    $errors = [];
    
    // Récupération et nettoyage des données du formulaire
    $courriel = filter_var(trim($_POST['courriel']), FILTER_SANITIZE_EMAIL);
    $courriel_confirmation = filter_var(trim($_POST['courriel_confirmation']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['motDePasse'];
    $mot_de_passe_confirmation = $_POST['confirm_password'];

    // Vérifiez si les champs sont vides
    if (empty($courriel) || empty($courriel_confirmation) || empty($mot_de_passe) || empty($mot_de_passe_confirmation)) {
        $errors[] = ERROR_EMPTY_FIELDS;
    }

    // Vérifiez si les courriels correspondent
    if ($courriel !== $courriel_confirmation) {
        $errors[] = "Les adresses courriel ne correspondent pas.";
    }

    // Vérifiez si les mots de passe correspondent
    if ($mot_de_passe !== $mot_de_passe_confirmation) {
        $errors[] = ERROR_PASSWORD_MISMATCH;
    }

    // Définir le motif de validation pour le mot de passe
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{5,15}$/';

    // Vérifier si le mot de passe respecte les critères
    if (!preg_match($passwordPattern, $mot_de_passe)) {
        $errors[] = ERROR_PASSWORD_INVALID;
    }

    // Vérifier l'existence de l'email dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si l'email est déjà utilisé
    if ($result->num_rows > 0) {
        $errors[] = ERROR_EMAIL_USED;
    }

    // Si aucune erreur, procéder à l'inscription
    if (empty($errors)) {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $query = "INSERT INTO utilisateurs (Courriel, MotDePasse) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $courriel, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['success'] = SUCCESS_REGISTRATION;
        } else {
            $_SESSION['error'] = ERROR_REGISTRATION;
        }
    } else {
        $_SESSION['errors'] = $errors;
    }

    $stmt->close();
    $conn->close();
}
?>

<!-- Affichage des messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success']; ?>
        <a href="login.php" class="btn btn-primary">Connexion</a>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['errors'])): ?>
    <div class="alert alert-danger">
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .submit-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function validateForm() {
            const email = document.getElementById('email').value;
            const emailConfirmation = document.getElementById('confirm_email').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('confirm_password').value;

            let valid = true;

            if (email !== emailConfirmation) {
                alert("Les adresses courriel ne correspondent pas.");
                valid = false;
            }

            if (password !== passwordConfirmation) {
                alert("Les mots de passe ne correspondent pas.");
                valid = false;
            }

            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{5,15}$/;
            if (!passwordPattern.test(password)) {
                alert("Le mot de passe doit comporter entre 5 et 15 caractères, inclure des lettres majuscules, minuscules et des chiffres.");
                valid = false;
            }

            return valid;
        }
    </script>
</head>
<body>

<form class="modify_profile_form" action="" method="POST" onsubmit="return validateForm()">
    <p class="titre">Inscription</p>

    <!-- Champ Courriel -->
    <label for="email">Courriel :</label>
    <input type="email" name="courriel" id="email" required>

    <!-- Confirmation du Courriel -->
    <label for="confirm_email">Confirmez le courriel :</label>
    <input type="email" name="courriel_confirmation" id="confirm_email" required>

    <!-- Champ Mot de Passe -->
    <label for="password">Mot de passe :</label>
    <input type="password" name="motDePasse" id="password" required>

    <!-- Confirmation du Mot de Passe -->
    <label for="confirm_password">Confirmez le mot de passe :</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <!-- Bouton de soumission -->
    <input type="submit" value="S'inscrire" class="submit-button">
</form>
</body>
</html>