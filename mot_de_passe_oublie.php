<?php
session_start();
include('config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Assurez-vous d'inclure PHPMailer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courriel = trim($_POST['courriel']);

    // Vérifier si l'utilisateur existe
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE Courriel = ?");
    $stmt->bind_param("s", $courriel);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Générer un token unique
        $token = bin2hex(random_bytes(50));

        // Stocker le token dans la base de données
        $stmt = $conn->prepare("UPDATE utilisateurs SET Token = ?, Modification = NOW() WHERE Courriel = ?");
        $stmt->bind_param("ss", $token, $courriel);
        $stmt->execute();

        // Vérifier si le token a été mis à jour
        if ($stmt->affected_rows > 0) {
            // Créer le lien de réinitialisation
            $resetLink = 'http://localhost/GestionAnnonces/reinitialiser_mot_de_passe.php?token=' . htmlentities($token);
            $mail = new PHPMailer(true);

            try {
                // Configuration du serveur SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'zarajeanfabrice@gmail.com'; // Votre courriel Gmail
                $mail->Password = 'mcskbtuzgqxatnwn'; // Votre mot de passe d'application Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Informations sur l'expéditeur et le destinataire
                $mail->setFrom('no-reply@example.com', 'Gestion Annonces');
                $mail->addAddress($courriel);

                // Contenu de l'e-mail
                $mail->Subject = 'Réinitialisation de votre mot de passe';
                $mail->isHTML(true);
                $mail->Body = "Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href='$resetLink'>Réinitialiser le mot de passe</a>";

                // Envoi de l'e-mail
                $mail->send();
                $_SESSION['success'] = "Un lien de réinitialisation a été envoyé à votre e-mail.";
            } catch (Exception $e) {
                echo "L'e-mail n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour du token.";
        }
    } else {
        $_SESSION['error'] = "Aucun utilisateur trouvé avec cet e-mail.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
</head>
<body>
    <form action="mot_de_passe_oublie.php" method="POST">
        <label for="courriel">Entrez votre courriel :</label>
        <input type="email" name="courriel" id="courriel" required>
        <input type="submit" value="Envoyer le lien de réinitialisation">
    </form>
    <div style="color:red;">
        <?php
        if (isset($_SESSION['error'])) {
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo $_SESSION['success'];
            unset($_SESSION['success']);
        }
        ?>
    </div>
</body>
</html>