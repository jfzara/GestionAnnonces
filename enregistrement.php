<?php
session_start();
include('config.php');

const ERROR_EMAIL_USED = "Cette adresse courriel est déjà utilisée.";
const ERROR_PASSWORD_MISMATCH = "Les mots de passe ne correspondent pas.";
const ERROR_PASSWORD_INVALID = "Le mot de passe doit comporter entre 5 et 15 caractères, inclure des lettres (majuscules et minuscules) et des chiffres.";
const ERROR_EMPTY_FIELDS = "Veuillez remplir tous les champs obligatoires.";
const ERROR_REGISTRATION = "Erreur lors de l'enregistrement.";
const SUCCESS_REGISTRATION = "Enregistrement réussi. Vous serez redirigé pour compléter votre profil.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    $courriel = filter_var(trim($_POST['courriel']), FILTER_SANITIZE_EMAIL);
    $courriel_confirmation = filter_var(trim($_POST['courriel_confirmation']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['motDePasse'];
    $mot_de_passe_confirmation = $_POST['confirm_password'];

    // Validation des champs
    if (empty($courriel) || empty($courriel_confirmation) || empty($mot_de_passe) || empty($mot_de_passe_confirmation)) {
        $errors[] = ERROR_EMPTY_FIELDS;
    }

    if ($courriel !== $courriel_confirmation) {
        $errors[] = "Les adresses courriel ne correspondent pas.";
    }

    if ($mot_de_passe !== $mot_de_passe_confirmation) {
        $errors[] = ERROR_PASSWORD_MISMATCH;
    }

    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{5,15}$/';
    if (!preg_match($passwordPattern, $mot_de_passe)) {
        $errors[] = ERROR_PASSWORD_INVALID;
    }

    // Vérifier l'existence de l'email dans la base de données
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = ERROR_EMAIL_USED;
    }

    // Inscription si aucune erreur
    if (empty($errors)) {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        $query = "INSERT INTO utilisateurs (Courriel, MotDePasse, Statut, Token) VALUES (?, ?, 0, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $courriel, $hashed_password, $token);

        if ($stmt->execute()) {
            $_SESSION['token'] = $token;
            header('Location: modifier_profil.php');
            exit();
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
            const email = document.getElementById('email').value.trim();
            const emailConfirmation = document.getElementById('confirm_email').value.trim();
            const password = document.getElementById('password').value.trim();
            const passwordConfirmation = document.getElementById('confirm_password').value.trim();

            let valid = true;

            if (!email || !emailConfirmation || !password || !passwordConfirmation) {
                alert("Veuillez remplir tous les champs obligatoires.");
                valid = false;
            }

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

    <label for="email">Courriel :</label>
    <input type="email" name="courriel" id="email" required>

    <label for="confirm_email">Confirmez le courriel :</label>
    <input type="email" name="courriel_confirmation" id="confirm_email" required>

    <label for="password">Mot de passe :</label>
    <input type="password" name="motDePasse" id="password" required>

    <label for="confirm_password">Confirmez le mot de passe :</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <input type="submit" value="S'inscrire" class="submit-button">
    <br>
    <p><a class="lien_enregistrement" href="login.php">Se connecter</a></p>
</form>
</body>
</html>