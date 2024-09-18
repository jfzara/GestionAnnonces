<?php 
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


session_start();

// Affichage du contenu de la session pour le débogage
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Récupérer le mot de passe depuis la session
$motDePasse = isset($_SESSION['motDePasse']) ? $_SESSION['motDePasse'] : null;

if ($motDePasse === null) {
    echo "Le mot de passe doit être disponible dans la session.";
    exit;
}

// Configurations de base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestionannonces";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $courriel = $_POST['courriel'] ?? '';
    $noTelMaison = $_POST['NoTelMaison'] ?? '';
    $noTelTravail = $_POST['NoTelTravail'] ?? '';
    $noTelCellulaire = $_POST['NoTelCellulaire'] ?? '';

    $champsVides = [];
    if (empty($nom)) $champsVides[] = 'nom';
    if (empty($prenom)) $champsVides[] = 'prenom';
    if (empty($courriel) || !filter_var($courriel, FILTER_VALIDATE_EMAIL)) $champsVides[] = 'courriel';
    
    // Validation des numéros de téléphone
    if (empty($noTelMaison) || !preg_match('/^[\d\s\(\)\-\_]+$/', $noTelMaison)) {
        $champsVides[] = 'NoTelMaison doit être valide.';
    }
    if (empty($noTelCellulaire) || !preg_match('/^[\d\s\(\)\-\_]+$/', $noTelCellulaire)) {
        $champsVides[] = 'NoTelCellulaire doit être valide.';
    }
    if (empty($noTelTravail) || !preg_match('/^[\d\s\(\)\-\_]+$/', $noTelTravail)) {
        $champsVides[] = 'NoTelTravail doit être valide.';
    }

    // Nettoyage des numéros de téléphone
    $noTelMaison = preg_replace('/[^\d]/', '', $noTelMaison);
    $noTelCellulaire = preg_replace('/[^\d]/', '', $noTelCellulaire);
    $noTelTravail = preg_replace('/[^\d]/', '', $noTelTravail);

    // Vérifier si l'email existe déjà
    $queryCheck = "SELECT COUNT(*) FROM utilisateurs WHERE Courriel = ?";
    $stmtCheck = $conn->prepare($queryCheck);
    $stmtCheck->bind_param("s", $courriel);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        die("L'email existe déjà. Veuillez utiliser un autre email.");
    }

    // Hacher le mot de passe
    $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(16));

    // Préparez la requête d'INSERT
    $query = 'INSERT INTO utilisateurs (Nom, Prenom, Courriel, NoTelMaison, NoTelCellulaire, NoTelTravail, Statut, Token, MotDePasse) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($query);
    $statut = 1; 
    $stmt->bind_param("ssssssiss", $nom, $prenom, $courriel, $noTelMaison, $noTelCellulaire, $noTelTravail, $statut, $token, $motDePasseHache);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);

        try {
            // Paramètres SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'zarajeanfabrice@gmail.com'; // Adresse email
            $mail->Password = 'mcskbtuzgqxatnwn'; // Mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Débogage SMTP pour voir les étapes
            $mail->SMTPDebug = 2;

            // Expéditeur et destinataire
            $mail->setFrom('zarajeanfabrice@gmail.com', 'Votre Nom');
            $mail->addAddress('destinataire@example.com');

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = 'Test';
            $mail->Body = 'Ceci est un test.';

            // Envoi de l'email
            $mail->send();
            echo 'Email envoyé avec succès';
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
        }
    }
    // Fermer la connexion à la base de données après l'envoi de l'email
    $conn->close();
} // Fermeture de la condition POST