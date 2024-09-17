<?php
// Inclure PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Assurez-vous que le chemin est correct

// Connexion à la base de données
$servername = "localhost";
$username = "root"; // Remplacez par votre nom d'utilisateur
$password = ""; // Remplacez par votre mot de passe
$dbname = "gestionannonces"; // Remplacez par le nom de votre base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Récupération des données du formulaire avec vérification
$nom = isset($_POST['nom']) ? $_POST['nom'] : '';
$prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
$courriel = isset($_POST['courriel']) ? $_POST['courriel'] : '';
$noTelBureau = isset($_POST['NoTelBureau']) ? $_POST['NoTelBureau'] : '';
$posteBureau = isset($_POST['PosteBureau']) ? $_POST['PosteBureau'] : '';
$noTelMaison = isset($_POST['NoTelMaison']) ? $_POST['NoTelMaison'] : '';
$noTelCellulaire = isset($_POST['NoTelCellulaire']) ? $_POST['NoTelCellulaire'] : '';
$noEmpl = isset($_POST['NoEmpl']) ? $_POST['NoEmpl'] : '';
$statut = isset($_POST['statut']) ? $_POST['statut'] : '';

// Debugging: afficher les valeurs
var_dump($nom, $prenom, $courriel, $noTelBureau, $posteBureau, $noTelMaison, $noTelCellulaire, $noEmpl, $statut);

// Vérification des champs
$champsVides = [];
if (empty($nom)) $champsVides[] = 'nom';
if (empty($prenom)) $champsVides[] = 'prenom';
if (empty($courriel)) $champsVides[] = 'courriel';
if (empty($noTelMaison)) $champsVides[] = 'NoTelMaison';
if (empty($noTelCellulaire)) $champsVides[] = 'NoTelCellulaire';
if (empty($noTelBureau)) $champsVides[] = 'NoTelBureau';
if (empty($noEmpl)) $champsVides[] = 'NoEmpl';

if (!empty($champsVides)) {
    die("Les champs suivants doivent être remplis : " . implode(', ', $champsVides));
}



// Préparez la requête d'UPDATE
$query = 'UPDATE utilisateurs SET Nom = ?, Prenom = ?, Courriel = ?, NoTelMaison = ?, NoTelCellulaire = ?, NoTelTravail = ?, Statut = 0 WHERE NoUtilisateur = ?';

// Préparation de la requête
$stmt = $conn->prepare($query);

// Liez les paramètres
$stmt->bind_param("ssssssi", $nom, $prenom, $courriel, $noTelMaison, $noTelCellulaire, $noTelTravail, $noUtilisateur);

// Exécutez la requête
if ($stmt->execute()) {
    // Génération d'un token unique
    $token = bin2hex(random_bytes(16));


    var_dump($noUtilisateur);
    // Mettez à jour le champ Token dans la base de données
    $updateTokenQuery = 'UPDATE utilisateurs SET Token = ? WHERE NoUtilisateur = ?';
    $stmtUpdateToken = $conn->prepare($updateTokenQuery);
    $stmtUpdateToken->bind_param("si", $token, $noUtilisateur);
    
    if ($stmtUpdateToken->execute()) {
        // Envoi de l'email de confirmation
        $mail = new PHPMailer(true);
        try {
            // Paramètres du serveur
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Remplacez par votre serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'zarajeanfabrice@gmail.com'; // Votre adresse email
            $mail->Password = 'limhgqahfpchjnch'; // Votre mot de passe email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ou PHPMailer::ENCRYPTION_SMTPS si nécessaire
            $mail->Port = 587;

            // Destinataires
            $mail->setFrom('from@example.com', 'Nom de l\'expéditeur');
            $mail->addAddress($courriel); // Email de l'utilisateur

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de votre inscription';
            $confirmationLink = "http://localhost/GestionAnnonces/confirmation.php?token=" . $token; // Lien de confirmation avec le token
            $mail->Body = "Merci de vous être inscrit! Cliquez sur le lien pour confirmer votre compte : <a href='$confirmationLink'>Confirmer mon compte</a>";

            $mail->send();
            echo 'Email de confirmation envoyé!';
        } catch (Exception $e) {
            echo "L'email n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
        }
    } else {
        echo "Erreur lors de la mise à jour du token: " . $stmtUpdateToken->error;
    }
} else {
    echo "Erreur lors de la mise à jour: " . $stmt->error;
}

// Fermez la connexion
$stmt->close();
$stmtUpdateToken->close();
$conn->close();
?>