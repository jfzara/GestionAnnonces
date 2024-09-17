<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  

session_start(); // Démarrer la session au début

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Le fichier .env est introuvable ou ne peut pas être lu : " . $e->getMessage());
}

include('config.php'); // Inclure le fichier de configuration pour la connexion à la base de données

const ERROR_EMAIL_USED = "Cette adresse courriel est déjà utilisée.";
const ERROR_PASSWORD_MISMATCH = "Les mots de passe ne correspondent pas.";
const ERROR_PASSWORD_INVALID = "Le mot de passe doit comporter entre 5 et 15 caractères, inclure des lettres (majuscules et minuscules) et des chiffres.";
const ERROR_EMPTY_FIELDS = "Veuillez remplir tous les champs obligatoires.";
const ERROR_REGISTRATION = "Erreur lors de l'enregistrement.";
const SUCCESS_REGISTRATION = "Inscription réussie. Veuillez compléter votre profil.";

// Vérifiez si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialisation des messages d'erreur
    $errors = [];
    
    // Récupération et nettoyage des données du formulaire
    $courriel = filter_var(trim($_POST['tbinscriptionEmail']), FILTER_SANITIZE_EMAIL);
    $courrielConfirmation = filter_var(trim($_POST['tbinscriptionEmailConfirmation']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['tbInscriptionMDP'];
    $mot_de_passe_confirmation = $_POST['tbInscriptionMDPConfirmation'];
    
    // Vérifiez si les champs sont vides
    if (empty($courriel) || empty($courrielConfirmation) || empty($mot_de_passe) || empty($mot_de_passe_confirmation)) {
        $errors[] = ERROR_EMPTY_FIELDS;
    }

    // Vérifiez si les mots de passe correspondent
    if ($mot_de_passe !== $mot_de_passe_confirmation) {
        $errors[] = ERROR_PASSWORD_MISMATCH;
    }

    // Vérifiez si les courriels correspondent
    if ($courriel !== $courrielConfirmation) {
        $errors[] = "Les courriels ne correspondent pas.";
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

    // Si des erreurs existent, retourner à la page d'enregistrement avec les messages d'erreur
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors; // Stocker les erreurs dans la session
    } else {
        // Stocker l'email et le mot de passe dans la session pour le formulaire suivant
        $_SESSION['courriel'] = $courriel;
        $_SESSION['mot_de_passe'] = $mot_de_passe; // Si besoin

        // Redirection vers modifier_profil.php
        header("Location: modifier_profil.php"); 
        exit();
    }
}
?>

<!-- Affichage du message de succès avec un bouton de connexion -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success']; ?>
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
</head>
<body>
    <div class="container col-md-5 jumbotron">
        <h2 class="text-center">Enregistrement</h2><br>
        <form id="formInscription" method="POST" action="">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Courriel</label>
                    <input type="email" class="form-control" id="tbinscriptionEmail" name="tbinscriptionEmail" placeholder="Courriel @" required>
                    <p id="errEmail" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Confirmation du Courriel</label>
                    <input type="email" class="form-control" id="tbinscriptionEmailConfirmation" name="tbinscriptionEmailConfirmation" placeholder="Confirmez le courriel" required>
                    <p id="errEmailConfirm" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Mot de passe</label>
                    <input type="password" class="form-control" id="tbInscriptionMDP" name="tbInscriptionMDP" placeholder="Mot de Passe" required>
                    <p id="errMdp" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Confirmation du Mot de passe</label>
                    <input type="password" class="form-control" id="tbInscriptionMDPConfirmation" name="tbInscriptionMDPConfirmation" placeholder="Confirmez le mot de passe" required>
                    <p id="errMdpConfirm" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
    </div>
</body>
</html>