<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestionannonces";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$courriel = $_POST['courriel'] ?? '';
$noTelBureau = $_POST['NoTelBureau'] ?? '';
$posteBureau = $_POST['PosteBureau'] ?? '';
$noTelMaison = $_POST['NoTelMaison'] ?? '';
$noTelCellulaire = $_POST['NoTelCellulaire'] ?? '';
$noTelTravail = $_POST['NoTelTravail'] ?? ''; // Ajout de cette ligne
$statut = "0"; // Définit le statut par défaut

$champsVides = [];
if (empty($nom)) $champsVides[] = 'nom';
if (empty($prenom)) $champsVides[] = 'prenom';
if (empty($courriel)) $champsVides[] = 'courriel';
if (empty($noTelMaison)) $champsVides[] = 'NoTelMaison';
if (empty($noTelCellulaire)) $champsVides[] = 'NoTelCellulaire';
if (empty($noTelBureau)) $champsVides[] = 'NoTelBureau';

if (!empty($champsVides)) {
    die("Les champs suivants doivent être remplis : " . implode(', ', $champsVides));
}

// Générez le token
$token = bin2hex(random_bytes(16));

// Préparez la requête d'INSERT
$query = 'INSERT INTO utilisateurs (Nom, Prenom, Courriel, NoTelMaison, NoTelCellulaire, NoTelTravail, Statut, Token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssssis", $nom, $prenom, $courriel, $noTelMaison, $noTelCellulaire, $noTelTravail, $statut, $token);

if ($stmt->execute()) {
    // Si l'insertion réussit, envoyez l'email de confirmation
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'zarajeanfabrice@gmail.com';
        $mail->Password = 'limhgqahfpchjnch'; // À remplacer par une variable d'environnement
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('from@example.com', 'Gestion Annonces');
        $mail->addAddress($courriel);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation de votre inscription';
        $confirmationLink = "http://localhost/GestionAnnonces/confirmation.php?token=" . $token;
        $mail->Body = "Merci de vous être inscrit! Cliquez sur le lien pour confirmer votre compte : <a href='$confirmationLink'>Confirmer mon compte</a>";

        $mail->send();
        echo 'Email de confirmation envoyé!';
    } catch (Exception $e) {
        echo "L'email n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
    }
} else {
    echo "Erreur lors de l'insertion: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>