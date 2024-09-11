<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Assurez-vous que ce chemin est correct

session_start(); // Démarrer la session au début

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
    $dotenv->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    die("Le fichier .env est introuvable ou ne peut pas être lu : " . $e->getMessage());
}

include('config.php');

// Vérifiez si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données de formulaire
    $courriel = trim($_POST['courriel']); // Récupérez l'e-mail de l'utilisateur
    $mot_de_passe = $_POST['password1'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);

    // Vérification de l'existence de l'email
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Cette adresse courriel est déjà utilisée.";
        header('Location: enregistrement.php');
        exit();
    } else {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16));

        // Insérer les données dans la base de données
        $stmt = $conn->prepare("INSERT INTO utilisateurs (Courriel, MotDePasse, Nom, Prenom, Statut, Token) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("sssss", $courriel, $hashed_password, $nom, $prenom, $token);
        
        if ($stmt->execute()) {
            // Envoi du courriel de confirmation avec PHP Mailer
            $mail = new PHPMailer(true);
            try {
                // Configuration du serveur SMTP
                $mail->isSMTP();
                $mail->Host       = $_ENV['SMTP_HOST'];  
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USERNAME']; 
                $mail->Password   = 'bxra mwqx rqfy osmm'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Destinataire et contenu
                $mail->setFrom($_ENV['SMTP_USERNAME'], 'Gestion Annonces');
                $mail->addAddress($courriel);

                // Contenu du message
                $mail->isHTML(true);
                $mail->Subject = 'Confirmation de votre inscription';
                $mail->Body    = "Bonjour $nom $prenom,<br><br>Merci pour votre inscription ! Veuillez confirmer votre adresse courriel en cliquant sur le lien suivant :<br><br>
                <a href='http://localhost/GestionAnnonces/confirmation.php?token=$token'>Confirmer votre compte</a><br><br>Si vous n'avez pas créé ce compte, ignorez ce courriel.";

                $mail->send();
                $_SESSION['success'] = "Enregistrement réussi. Veuillez vérifier votre courriel pour confirmer votre inscription.";
                header('Location: login.php?message=Vérifiez votre courriel pour confirmer votre compte');
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Le courriel n'a pas pu être envoyé. Erreur : " . $mail->ErrorInfo;
                header('Location: enregistrement.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Erreur lors de l'enregistrement.";
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
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red;'>".$_SESSION['error']."</p>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<p style='color:green;'>".$_SESSION['success']."</p>";
        unset($_SESSION['success']);
    }
    ?>

    <form action="enregistrement.php" method="POST">
        <label for="email1">Courriel :</label>
        <input type="email" name="courriel" id="email1" required value="<?php echo isset($_POST['courriel']) ? htmlspecialchars($_POST['courriel']) : ''; ?>">
        <br>
        <label for="password1">Mot de passe :</label>
        <input type="password" name="password1" id="password1" required>
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