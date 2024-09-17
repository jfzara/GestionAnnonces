<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  

session_start(); // Démarrer la session au début

// Charger les variables d'environnement à partir du fichier .env + gestion si .env est introuvable
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Le fichier .env est introuvable ou ne peut pas être lu : " . $e->getMessage());
}

include('config.php'); // Inclure le fichier de configuration pour la connexion à la base de données
$mail = new PHPMailer();
const ERROR_EMAIL_USED = "Cette adresse courriel est déjà utilisée.";
const ERROR_PASSWORD_MISMATCH = "Les mots de passe ne correspondent pas.";
const ERROR_PASSWORD_INVALID = "Le mot de passe doit comporter entre 5 et 15 caractères, inclure des lettres (majuscules et minuscules) et des chiffres.";
const ERROR_EMPTY_FIELDS = "Veuillez remplir tous les champs obligatoires.";
const ERROR_REGISTRATION = "Erreur lors de l'enregistrement.";
const SUCCESS_REGISTRATION = "Enregistrement réussi. Veuillez vérifier votre courriel pour confirmer votre inscription.";
const SUCCESS_ADMIN_REGISTRATION = "Enregistrement réussi en tant que ADMINISTRATEUR."; // Message de succès pour les admins

// Vérifiez si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialisation des messages d'erreur
    $errors = [];
    
    // Récupération et nettoyage des données du formulaire
    $courriel = filter_var(trim($_POST['courriel']), FILTER_SANITIZE_EMAIL);
    $courriel_confirmation = filter_var(trim($_POST['courriel_confirmation']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['password1'];
    $mot_de_passe_confirmation = $_POST['password2']; // Champ de confirmation

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

    // Vérification de l'existence de l'email
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si l'email est déjà utilisé
    if ($result->num_rows > 0) {
        $errors[] = ERROR_EMAIL_USED;
    }

    if (empty($errors)) {
        // Hachage du mot de passe
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    
        // Insérer les données dans la base de données uniquement avec le courriel et le mot de passe
        $query = "INSERT INTO utilisateurs (Courriel, MotDePasse) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        
        // Lier les variables qui correspondent aux paramètres de la requête
        $stmt->bind_param("ss", $courriel, $hashed_password);
    
        // Exécuter la requête
        if ($stmt->execute()) {
            // Envoi du courriel de confirmation à l'utilisateur
            // Configuration de PHPMailer comme dans votre code existant...
        } else {
            $_SESSION['error'] = ERROR_REGISTRATION;
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!-- Affichage du message de succès avec un bouton de connexion -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success']; ?>
        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
            <br><a href="login.php" class="btn btn-primary">Connexion</a>
        <?php endif; ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<!-- Affichage des messages d'erreur -->
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
    <script>
        function toggleAdminCheckbox() {
            const emailInput = document.getElementById('email1').value;
            const adminCheckbox = document.getElementById('adminSection');
            if (emailInput === 'admin@gmail.com') {
                adminCheckbox.style.display = 'block'; // Afficher la section admin
            } else {
                adminCheckbox.style.display = 'none'; // Masquer la section admin
                document.getElementById('isAdmin').checked = false; // Désélectionner le checkbox si visible
            }
        }

        function validateForm() {
            const email = document.getElementById('email1').value;
            const emailConfirmation = document.getElementById('email2').value;
            const password = document.getElementById('password1').value;
            const passwordConfirmation = document.getElementById('password2').value;

            let valid = true;

            // Vérifier si les emails correspondent
            if (email !== emailConfirmation) {
                alert("Les adresses courriel ne correspondent pas.");
                valid = false;
            }

            // Vérifier si les mots de passe correspondent
            if (password !== passwordConfirmation) {
                alert("Les mots de passe ne correspondent pas.");
                valid = false;
            }

            return valid; // Retourne vrai si tout est valide, sinon faux
        }
    </script>
</head>
<body>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styles pour le bouton */
        .submit-button {
            background-color: #007bff; /* Couleur de fond bleu */
            color: white; /* Couleur du texte */
            border: none; /* Pas de bordure */
            padding: 10px 20px; /* Espacement intérieur */
            font-size: 16px; /* Taille de la police */
            cursor: pointer; /* Curseur pointer */
            border-radius: 5px; /* Coins arrondis */
            transition: background-color 0.3s; /* Transition douce pour le survol */
        }

        .submit-button:hover {
            background-color: #0056b3; /* Couleur au survol */
        }
    </style>
    <script>
        function toggleAdminCheckbox() {
            const emailInput = document.getElementById('email1').value;
            const adminCheckbox = document.getElementById('adminSection');
            if (emailInput === 'admin@gmail.com') {
                adminCheckbox.style.display = 'block'; // Afficher la section admin
            } else {
                adminCheckbox.style.display = 'none'; // Masquer la section admin
                document.getElementById('isAdmin').checked = false; // Désélectionner le checkbox si visible
            }
        }

        function validateForm() {
            const email = document.getElementById('email1').value;
            const emailConfirmation = document.getElementById('email2').value;
            const password = document.getElementById('password1').value;
            const passwordConfirmation = document.getElementById('password2').value;

            let valid = true;

            // Vérifier si les emails correspondent
            if (email !== emailConfirmation) {
                alert("Les adresses courriel ne correspondent pas.");
                valid = false;
            }

            // Vérifier si les mots de passe correspondent
            if (password !== passwordConfirmation) {
                alert("Les mots de passe ne correspondent pas.");
                valid = false;
            }

            return valid; // Retourne vrai si tout est valide, sinon faux
        }
    </script>
</head>
<body>

    <form class="register_form" action="modifier_profil.php" method="POST" onsubmit="return validateForm();">
        <p class="titre">Inscription</p>
        
        <label for="email1">Courriel :</label>
        <input type="email" name="courriel" id="email1" required oninput="toggleAdminCheckbox();" value="<?php echo isset($courriel) ? htmlspecialchars($courriel) : ''; ?>">

        <label for="email2">Confirmez le courriel :</label>
        <input type="email" name="courriel_confirmation" id="email2" required value="<?php echo isset($courriel) ? htmlspecialchars($courriel) : ''; ?>">

        <label for="password1">Mot de passe :</label>
        <input type="password" name="password1" id="password1" required>

        <label for="password2">Confirmez le mot de passe :</label>
        <input type="password" name="password2" id="password2" required>

        <!-- Section pour inscrire en tant qu'administrateur -->
        <div id="adminSection" style="display: none;">
            <label>
                <input type="checkbox" name="isAdmin" value="1" id="isAdmin"> Inscrire en tant qu'administrateur
            </label>
        </div>

        <input type="submit" value="S'inscrire" class="submit-button">
    </form>
</body>
</html>
