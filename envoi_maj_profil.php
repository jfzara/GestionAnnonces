<?php
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestionannonces";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données soumises
    $nom = htmlspecialchars($_POST['nom'] ?? '');
    $prenom = htmlspecialchars($_POST['prenom'] ?? '');
    $courriel = filter_var($_POST['courriel'] ?? '', FILTER_VALIDATE_EMAIL) ? $_POST['courriel'] : '';
    $noTelMaison = htmlspecialchars($_POST['NoTelMaison'] ?? '');
    $noTelTravail = htmlspecialchars($_POST['NoTelTravail'] ?? '');
    $noTelCellulaire = htmlspecialchars($_POST['NoTelCellulaire'] ?? '');
    
    // Si NoEmpl est vide, le remplacer par NULL
    $NoEmpl = isset($_POST['NoEmpl']) && !empty($_POST['NoEmpl']) ? htmlspecialchars($_POST['NoEmpl']) : NULL;

    // Validation des champs
    $champsVides = [];
    if (empty($nom)) $champsVides[] = 'nom';
    if (empty($prenom)) $champsVides[] = 'prenom';
    if (empty($courriel)) $champsVides[] = 'courriel invalide';
    if (!preg_match('/^[\d\s\(\)\-\_]+$/', $noTelMaison)) $champsVides[] = 'NoTelMaison invalide';
    if (!preg_match('/^[\d\s\(\)\-\_]+$/', $noTelTravail)) $champsVides[] = 'NoTelTravail invalide';
    if (!preg_match('/^[\d\s\(\)\-\_]+$/', $noTelCellulaire)) $champsVides[] = 'NoTelCellulaire invalide';

    // Afficher les erreurs de validation si elles existent
    if (!empty($champsVides)) {
        foreach ($champsVides as $champ) {
            echo "<div style='color: red;'>Le champ $champ est requis ou invalide.</div>";
        }
    } else {
        // Nettoyage des numéros de téléphone
        $noTelMaison = preg_replace('/[^\d]/', '', $noTelMaison);
        $noTelTravail = preg_replace('/[^\d]/', '', $noTelTravail);
        $noTelCellulaire = preg_replace('/[^\d]/', '', $noTelCellulaire);

        // Vérification de l'existence de l'utilisateur
        $queryCheck = 'SELECT * FROM utilisateurs WHERE Courriel = ?';
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->bind_param("s", $courriel);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            // Utilisateur trouvé, récupération du token
            $utilisateur = $resultCheck->fetch_assoc();
            $token = $utilisateur['Token'];

            // Requête de mise à jour
            $queryUpdate = 'UPDATE utilisateurs SET Nom = ?, Prenom = ?, NoTelMaison = ?, NoTelTravail = ?, NoTelCellulaire = ?, NoEmpl = ? WHERE Courriel = ?';
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->bind_param("sssssss", $nom, $prenom, $noTelMaison, $noTelTravail, $noTelCellulaire, $NoEmpl, $courriel);
            $stmtUpdate->execute();

            // Envoi de l'email de confirmation
            $_SESSION['message'] = "Un courriel de confirmation a été envoyé à <strong>$courriel</strong>.";

            $lienConfirmation = 'http://localhost/GestionAnnonces/confirmation.php?token=' . htmlentities($token);
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'zarajeanfabrice@gmail.com';
                $mail->Password = 'mcskbtuzgqxatnwn'; // Votre mot de passe d'application
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('no-reply@example.com', 'Gestion Annonces');
                $mail->addAddress($courriel);
                $mail->Subject = 'Confirmation';
                $mail->isHTML(true);
                $mail->Body = "Merci pour votre mise à jour! Pour confirmer votre compte, veuillez cliquer sur le lien suivant : <a href='$lienConfirmation'>Confirmer</a>";

                $mail->send();
            } catch (Exception $e) {
                echo "L'email de confirmation n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
            }
        } else {
            echo 'Aucun utilisateur trouvé avec cet email : ' . htmlentities($courriel);
        }

        // Fermer la connexion
        $stmtCheck->close();
        $conn->close();
    }
}
?>

<!-- Affichage du message de confirmation -->
<div style="display: flex; justify-content: center; align-items: center; height: 100vh;">
    <?php if (isset($_SESSION['message'])): ?>
        <div style="background-color: white; padding: 3rem; text-align: center; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
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
    <input type="text" name="NoEmpl" placeholder="Numéro Employé (optionnel)">
    <button type="submit">Mettre à jour</button>
</form>