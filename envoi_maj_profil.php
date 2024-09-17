<?php
session_start();
require 'vendor/autoload.php'; // Inclure le fichier autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclure la configuration de la base de données
include('config.php'); 

$message = ""; // Initialiser le message

// Vérifiez si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification de l'existence des champs du formulaire
    $nom = isset($_POST['tbNom']) ? trim($_POST['tbNom']) : '';
    $prenom = isset($_POST['tbPrenom']) ? trim($_POST['tbPrenom']) : '';
    $email = isset($_POST['tbEmail']) ? filter_var(trim($_POST['tbEmail']), FILTER_SANITIZE_EMAIL) : '';
    $password = isset($_POST['tbPassword']) ? trim($_POST['tbPassword']) : '';

    // Vérification de l'e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div style='color: red;'>Email invalide.</div>";
    } else {
        // Générer un token de confirmation
        $token = bin2hex(random_bytes(16)); // Générez un token aléatoire de 32 caractères

        // Requête d'insertion
        $query = 'INSERT INTO utilisateurs (Nom, Prenom, Courriel, MotDePasse, Statut, Token) VALUES (?, ?, ?, ?, 0, ?)';
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Hash du mot de passe avant de le stocker
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('sssss', $nom, $prenom, $email, $hashedPassword, $token);
            
            if ($stmt->execute()) {
                // Envoi de l'e-mail de confirmation
                $mail = new PHPMailer(); // Utilisez cette ligne
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Remplacez par votre hôte SMTP
                $mail->SMTPAuth = true;
                $mail->Username = 'zarajeanfabrice@gmail.com'; // Votre adresse e-mail
                $mail->Password = 'lybddpkiorncgsxs'; // Votre mot de passe
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Destinataires
                $mail->setFrom('your_email@example.com', 'Nom de votre application');
                $mail->addAddress($email);

                // Contenu
                $mail->isHTML(true);
                $mail->Subject = 'Confirmation de votre compte';
                $mail->Body    = "Bonjour $prenom,<br><br>Merci de vous être inscrit. Veuillez cliquer sur le lien ci-dessous pour confirmer votre compte :<br><a href='http://yourdomain.com/confirmation.php?token=$token'>Confirmer mon compte</a>";

                if ($mail->send()) {
                    // Message de succès
                    $message = "Pour confirmer votre inscription, veuillez cliquer sur le lien envoyé à <strong>$email</strong>.";
                } else {
                    $message = "<div style='color: red;'>Erreur lors de l'envoi de l'e-mail de confirmation.</div>";
                }
            } else {
                $message = "<div style='color: red;'>Erreur lors de l'enregistrement : " . $stmt->error . "</div>";
            }

            $stmt->close();
        } else {
            $message = "<div style='color: red;'>Erreur lors de la préparation de la requête : " . $conn->error . "</div>";
        }
    }
}

// Fermer la connexion
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <a href="login.php" class="nav-item">Se connecter</a>
    </nav>

    <div>
        <!-- Affichage du message de succès ou d'erreur -->
        <?php if (!empty($message)) echo $message; ?>
    </div>
</body>
</html>
 