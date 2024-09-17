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
    $mot_de_passe = $_POST['password1'];
    $mot_de_passe_confirmation = $_POST['password2']; // Champ de confirmation
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $noTelMaison = trim($_POST['noTelMaison']);
    $noTelTravail = trim($_POST['noTelTravail']);
    $noTelCellulaire = trim($_POST['noTelCellulaire']);
    
    // Vérifiez si les champs sont vides
    if (empty($courriel) || empty($mot_de_passe) || empty($mot_de_passe_confirmation) || empty($nom) || empty($prenom) || empty($noTelMaison) || empty($noTelTravail) || empty($noTelCellulaire)) {
        $errors[] = ERROR_EMPTY_FIELDS;
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

    // Si des erreurs existent, retourner à la page d'enregistrement avec les messages d'erreur
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors; // Stocker les erreurs dans la session
    } else {
        // Hachage du mot de passe et génération d'un token
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        // Déterminer le statut (admin ou utilisateur normal)
        $statut = 0; // Par défaut, statut utilisateur normal
        if (isset($_POST['isAdmin']) && $_POST['isAdmin'] === '1' && $courriel === 'admin@gmail.com') {
            $statut = 1; // 1 pour admin
        }

        // Insérer les données dans la base de données
        $query = "INSERT INTO utilisateurs (Courriel, MotDePasse, Nom, Prenom, Statut, Token, NoTelMaison, NoTelTravail, NoTelCellulaire)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Lier les variables qui correspondent aux paramètres de la requête
        $stmt->bind_param("sssssssss", $courriel, $hashed_password, $nom, $prenom, $statut, $token, $noTelMaison, $noTelTravail, $noTelCellulaire);

        // Exécuter la requête
        if ($stmt->execute()) {
            // Envoi du courriel de confirmation à l'utilisateur
            $mail = new PHPMailer(true);
            try {
                // Configuration du serveur SMTP
                $mail->isSMTP();
                $mail->Host       = $_ENV['SMTP_HOST'];  
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USERNAME']; 
                $mail->Password   = $_ENV['SMTP_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Destinataire et contenu
                $mail->setFrom('zarajeanfabrice@gmail.com', 'Gestion Annonces');
                $mail->addAddress($courriel); // Envoi au nouvel utilisateur

                // Contenu du message de confirmation à l'utilisateur
                $mail->isHTML(true);
                $mail->Subject = 'Confirmation de votre inscription';
                $mail->Body    = "Bonjour $nom $prenom,<br><br>Merci pour votre inscription ! Veuillez confirmer votre adresse courriel en cliquant sur le lien suivant :<br><br>
                <a href='http://localhost/GestionAnnonces/confirmation.php?token=$token'>Confirmer votre compte</a><br><br>Si vous n'avez pas créé ce compte, ignorez ce courriel.";
                
                $mail->send();

                // Stocker un message de succès dans la session pour affichage
                $_SESSION['success'] = ($statut === 1) ? SUCCESS_ADMIN_REGISTRATION : SUCCESS_REGISTRATION;
                $_SESSION['isAdmin'] = $statut === 1; // Stocker le statut dans la session pour l'affichage

            } catch (Exception $e) {
                $_SESSION['error'] = "Le courriel n'a pas pu être envoyé. Erreur : " . $mail->ErrorInfo;
            }
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
    </script>
</head>
<body>

    <form class="register_form" action="enregistrement.php" method="POST" onsubmit="toggleAdminCheckbox();">
        <p class="titre">Inscription</p>
        <label for="email1">Courriel :</label>
        <input type="email" name="courriel" id="email1" required oninput="toggleAdminCheckbox();" value="<?php echo isset($courriel) ? $courriel : ''; ?>">

        <label for="password1">Mot de passe :</label>
        <input type="password" name="password1" id="password1" required>

        <label for="password2">Confirmez le mot de passe :</label>
        <input type="password" name="password2" id="password2" required>

        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required>

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" required>

        <label for="noTelMaison">Numéro de téléphone (Maison) :</label>
        <input type="tel" name="noTelMaison" id="noTelMaison" required>

        <label for="noTelTravail">Numéro de téléphone (Travail) :</label>
        <input type="tel" name="noTelTravail" id="noTelTravail" required>

        <label for="noTelCellulaire">Numéro de téléphone (Cellulaire) :</label>
        <input type="tel" name="noTelCellulaire" id="noTelCellulaire" required>

        <!-- Section pour inscrire en tant qu'administrateur -->
        <div id="adminSection" style="display: none;">
            <label>
                <input type="checkbox" name="isAdmin" value="1" id="isAdmin"> Inscrire en tant qu'administrateur
            </label>
        </div>

        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>