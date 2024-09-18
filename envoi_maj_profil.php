<?php 
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

// Récupérer le mot de passe depuis la session
$motDePasse = $_SESSION['motDePasse'] ?? null;

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

    // Validation des champs
    $champsVides = [];
    if (empty($nom)) $champsVides[] = 'nom';
    if (empty($prenom)) $champsVides[] = 'prenom';
    if (empty($courriel) || !filter_var($courriel, FILTER_VALIDATE_EMAIL)) $champsVides[] = 'courriel';
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

    // Vérification de l'existence de l'utilisateur
    $queryCheck = 'SELECT * FROM utilisateurs WHERE Courriel = ?';
    $stmtCheck = $conn->prepare($queryCheck);
    $stmtCheck->bind_param("s", $courriel);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Récupérer l'utilisateur pour obtenir le token
        $utilisateur = $resultCheck->fetch_assoc();
        $token = $utilisateur['Token']; // Remplacez 'Token' par le nom de votre colonne de token

        // Email existe déjà, donc mise à jour des informations
        $_SESSION['message'] = "Un courriel de confirmation a été envoyé à <strong>$courriel</strong>.";

        // Envoi de l'email de confirmation avec le lien
        $lienConfirmation = 'http://localhost/GestionAnnonces/confirmation.php?token=' . $token; // Utiliser le token récupéré
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Spécifiez le serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'zarajeanfabrice@gmail.com'; // Votre adresse e-mail
            $mail->Password = 'mcskbtuzgqxatnwn'; // Votre mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer le chiffrement TLS
            $mail->Port = 587;

            $mail->setFrom('no-reply@example.com', 'Gestion Annonces');
            $mail->addAddress($courriel);
            $mail->Subject = 'Confirmation de mise à jour';
            $mail->Body = "Merci pour votre inscription! Pour confirmer votre compte, veuillez cliquer sur le lien suivant : <a href='$lienConfirmation'>Confirmer</a>";

            $mail->send();
        } catch (Exception $e) {
            echo "L'email de confirmation n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
        }
    } else {
        // Si l'utilisateur n'existe pas, insérer un nouvel utilisateur avec statut 0 (en attente)
        $token = bin2hex(random_bytes(16)); // Générer un token unique
        $queryInsert = 'INSERT INTO utilisateurs (Nom, Prenom, Courriel, NoTelMaison, NoTelTravail, NoTelCellulaire, Token, Statut) VALUES (?, ?, ?, ?, ?, ?, ?, 0)';
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bind_param("ssssssss", $nom, $prenom, $courriel, $noTelMaison, $noTelTravail, $noTelCellulaire, $token);
        $stmtInsert->execute();

        $_SESSION['message'] = "Votre inscription est en cours. Un courriel de confirmation a été envoyé à <strong>$courriel</strong>.";

        // Envoi de l'email de confirmation
        $lienConfirmation = 'http://localhost/GestionAnnonces/confirmation.php?token=' . $token;
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Spécifiez le serveur SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'zarajeanfabrice@gmail.com'; // Votre adresse e-mail
            $mail->Password = 'mcskbtuzgqxatnwn'; // Votre mot de passe d'application
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer le chiffrement TLS
            $mail->Port = 587;

            $mail->setFrom('no-reply@example.com', 'Gestion Annonces');
            $mail->addAddress($courriel);
            $mail->Subject = 'Confirmation de votre inscription';
            $mail->Body = "Merci pour votre inscription! Pour confirmer votre compte, veuillez cliquer sur le lien suivant : <a href='$lienConfirmation'>Confirmer</a>";

            $mail->send();
        } catch (Exception $e) {
            echo "L'email de confirmation n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
        }
    }

    // Fermer la connexion à la base de données
    $conn->close();
}
?>

<!-- Affichage du message de confirmation et du formulaire -->
<div style="margin-bottom: 20px;">
    <?php if (isset($_SESSION['message'])): ?>
        <div style="color: green;">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); // Supprimer le message après l'affichage ?>
        </div>
    <?php endif; ?>
</div>

<form action="" method="post">
    <input type="text" name="nom" placeholder="Nom" required>
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="email" name="courriel" placeholder="Courriel" required>
    <input type="text" name="NoTelMaison" placeholder="Téléphone Maison" required>
    <input type="text" name="NoTelTravail" placeholder="Téléphone Travail" required>
    <input type="text" name="NoTelCellulaire" placeholder="Téléphone Cellulaire" required>
    <button type="submit">Mettre à jour</button>
</form>