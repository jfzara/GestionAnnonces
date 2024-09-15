<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    echo "<div style='text-align: center; color: red;'>Veuillez vous connecter.</div>";
    exit;
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'gestionannonces');

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Vérifier si un ID d'annonce est passé en paramètre
if (isset($_GET['id'])) {
    $annonceId = $_GET['id'];

    // Préparer la requête pour obtenir les détails de l'annonce
    $stmt = $conn->prepare("SELECT * FROM annonces WHERE NoAnnonce = ?");
    $stmt->bind_param("i", $annonceId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si l'annonce existe
    if ($result->num_rows > 0) {
        $annonce = $result->fetch_assoc();
    } else {
        echo "<div style='text-align: center; color: red;'>Annonce non trouvée.</div>";
        exit;
    }
} else {
    echo "<div style='text-align: center; color: red;'>ID d'annonce manquant.</div>";
    exit;
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descriptionAbregee = $_POST['descriptionAbregee'];
    $descriptionComplete = $_POST['descriptionComplete'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    
    // Gestion de l'upload de la photo
    $targetDir = "photos-annonces/";
    $uploadOk = 1;
    $photo = $_FILES['photo']['name'];
    $targetFile = $targetDir . basename($photo);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Vérifier si un fichier a été téléchargé
    if ($_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        // Vérifier si le fichier est une image
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if ($check === false) {
            echo "<div style='text-align: center; color: red;'>Le fichier n'est pas une image.</div>";
            $uploadOk = 0;
        }

        // Vérifier la taille du fichier
        if ($_FILES['photo']['size'] > 500000) { // Limite à 500KB
            echo "<div style='text-align: center; color: red;'>Le fichier est trop gros.</div>";
            $uploadOk = 0;
        }

        // Autoriser certains formats de fichiers
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            echo "<div style='text-align: center; color: red;'>Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.</div>";
            $uploadOk = 0;
        }

        // Vérifier si tout est bon pour le téléchargement
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                // L'upload s'est bien passé
            } else {
                echo "<div style='text-align: center; color: red;'>Une erreur est survenue lors du téléchargement du fichier.</div>";
            }
        }
    } else {
        // Si aucun nouveau fichier n'est téléchargé, garder l'ancien fichier
        $targetFile = $annonce['Photo'];
    }

    // Préparer la requête pour mettre à jour l'annonce
    $updateStmt = $conn->prepare("UPDATE annonces SET DescriptionAbregee = ?, DescriptionComplete = ?, Prix = ?, Categorie = ?, Photo = ? WHERE NoAnnonce = ?");
    $updateStmt->bind_param("ssissi", $descriptionAbregee, $descriptionComplete, $prix, $categorie, $targetFile, $annonceId);

    if ($updateStmt->execute()) {
        echo "<div style='text-align: center; color: green; font-weight: bold; padding-bottom: 2rem;'>Annonce mise à jour avec succès!</div>";
    } else {
        echo "<div style='text-align: center; color: red;'>Erreur lors de la mise à jour de l'annonce.</div>";
    }

    $updateStmt->close();
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Annonce</title>
    <link rel="stylesheet" href="styles.css">
   <style>
        form  {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background: #dfe0e4;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            min-width: 36%;
            padding-left: 1vw;
            padding-right: 3vw;
            position: relative;
            top: 0;
        }
   </style>
</head>
<body>
    <nav class="navbar">
        <a href="annonces.php">Annonces</a>
        <a href="gestion_annonces.php">Gestion de vos annonces</a>
        <a href="modifier_profil.php">Modification du profil</a>
        <a href="Deconnexion.php">Déconnexion</a>
    </nav>

    <form class="update_form" method="POST" action="" enctype="multipart/form-data">
        <h2>Modifier l'Annonce</h2>
        <label for="descriptionAbregee">Description Abregee:</label>
        <input type="text" name="descriptionAbregee" id="descriptionAbregee" value="<?php echo htmlspecialchars($annonce['DescriptionAbregee']); ?>" required>

        <label for="descriptionComplete">Description Complète:</label>
        <textarea name="descriptionComplete" id="descriptionComplete" required><?php echo htmlspecialchars($annonce['DescriptionComplete']); ?></textarea>

        <label for="prix">Prix:</label>
        <input type="number" name="prix" id="prix" value="<?php echo htmlspecialchars($annonce['Prix']); ?>" required>

        <label for="categorie">Catégorie:</label>
        <input type="number" name="categorie" id="categorie" value="<?php echo htmlspecialchars($annonce['Categorie']); ?>" required>

        <label for="photo">Photo:</label>
        <input type="file" name="photo" id="photo">
        <p>Actuellement: <?php echo htmlspecialchars($annonce['Photo']); ?></p>

        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</body>
</html>