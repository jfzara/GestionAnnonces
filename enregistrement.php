<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  

session_start(); // Démarrer la session au début

// Charger les variables d'environnement à partir du fichier .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Le fichier .env est introuvable ou ne peut pas être lu : " . $e->getMessage());
}

include('config.php'); // Inclure le fichier de configuration pour la connexion à la base de données

const ERROR_EMAIL_USED = "Cette adresse courriel est déjà utilisée.";
const ERROR_PASSWORD_MISMATCH = "Les mots de passe ne correspondent pas.";
const ERROR_REGISTRATION = "Erreur lors de l'enregistrement.";
const SUCCESS_REGISTRATION = "Enregistrement réussi. Veuillez vérifier votre courriel pour confirmer votre inscription.";

// Vérifiez si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = filter_var(trim($_POST['courriel']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['password1'];
    $mot_de_passe_confirmation = $_POST['password2']; // Champ de confirmation
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);

    // Vérifiez si les mots de passe correspondent
    if ($mot_de_passe !== $mot_de_passe_confirmation) {
        $_SESSION['error'] = ERROR_PASSWORD_MISMATCH;
        header('Location: enregistrement.php');
        exit();
    }

    // Vérification de l'existence de l'email
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si l'email est déjà utilisé
    if ($result->num_rows > 0) {
        $_SESSION['error'] = ERROR_EMAIL_USED;
        header('Location: enregistrement.php');
        exit();
    } else {
        // Hachage du mot de passe et génération d'un token
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        // Insérer les données dans la base de données
        $stmt = $conn->prepare("INSERT INTO utilisateurs (Courriel, MotDePasse, Nom, Prenom, Statut, Token) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("sssss", $courriel, $hashed_password, $nom, $prenom, $token);
        
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

                // Redirection avec succès
                $_SESSION['success'] = SUCCESS_REGISTRATION;
                header('Location: login.php?message=Vérifiez votre courriel pour confirmer votre compte');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Le courriel n'a pas pu être envoyé. Erreur : " . $mail->ErrorInfo;
                header('Location: enregistrement.php');
                exit();
            }
        } else {
            $_SESSION['error'] = ERROR_REGISTRATION;
            header('Location: enregistrement.php');
            exit();
        }
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrement</title>
</head>
<body>
    <h1>Inscription</h1>

    <?php
    // Affichage des messages d'erreur ou de succès
    foreach (['error', 'success'] as $msg) {
        if (isset($_SESSION[$msg])) {
            echo "<p style='color:".($msg === 'error' ? 'red' : 'green').";'>".$_SESSION[$msg]."</p>";
            unset($_SESSION[$msg]);
        }
    }
    ?>

    <form action="enregistrement.php" method="POST">
        <label for="email1">Courriel :</label>
        <input type="email" name="courriel" id="email1" required value="<?php echo isset($_POST['courriel']) ? htmlspecialchars($_POST['courriel']) : ''; ?>">
        <br>
        <label for="password1">Mot de passe :</label>
        <input type="password" name="password1" id="password1" required>
        <br>
        <label for="password2">Confirmer le mot de passe :</label>
        <input type="password" name="password2" id="password2" required>
        <br>
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
        <br>
        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" required value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
        <br>
        <input type="submit" value="S'enregistrer">
    </form>
</body>
</html>