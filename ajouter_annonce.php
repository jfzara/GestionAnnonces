<?php
// Inclure la connexion à la base de données
include 'db.php';
session_start(); // Assurez-vous que la session est démarrée

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['NoUtilisateur'])) {
    die("Accès non autorisé. Veuillez vous connecter.");
}

// Récupérer l'ID de l'utilisateur connecté
$noUtilisateur = $_SESSION['NoUtilisateur'];

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $categorie = $_POST['ddlCategories'];
    $petiteDesc = $_POST['tbPetiteDesc'];
    $grosseDesc = $_POST['tbGrosseDesc'];
    $prix = $_POST['tbPrixAnnonce'];
    $active = $_POST['Activation'];

    // Gestion de l'upload d'image
    $targetDir = "photos-annonces/";
    $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Vérifier si le fichier est une image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check === false) {
        echo "Ce fichier n'est pas une image.";
        $uploadOk = 0;
    }

    // Vérifier la taille du fichier
    if ($_FILES["fileToUpload"]["size"] > 100000) {
        echo "Désolé, votre fichier est trop volumineux.";
        $uploadOk = 0;
    }

    // Autoriser certains formats de fichiers
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        $uploadOk = 0;
    }

    // Télécharger l'image si tout est correct
    if ($uploadOk == 1 && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
        echo "Le fichier " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " a été téléchargé. ";
    } else {
        echo "Désolé, une erreur est survenue lors du téléchargement de votre fichier.";
        $targetFile = ""; // Mettre à jour si le fichier n'a pas été téléchargé
    }

    // Insérer les données dans la base de données
    if ($uploadOk == 1) {
        try {
            $query = "INSERT INTO annonces (NoUtilisateur, Categorie, DescriptionAbregee, DescriptionComplete, Prix, Photo, Etat) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iissdsi", $noUtilisateur, $categorie, $petiteDesc, $grosseDesc, $prix, $targetFile, $active);
            $stmt->execute();

            echo "Annonce ajoutée avec succès !";
        } catch (mysqli_sql_exception $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une annonce</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container col-md-5 jumbotron">
    <h2 class="text-center">Ajouter une annonce</h2>
    <form enctype="multipart/form-data" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000">

        <div class="form-group">
            <label>Catégorie</label>
            <select name="ddlCategories" class="form-control" required>
                <option value="0">Sélectionner une catégorie</option>
                <option value="1">Location</option>
                <option value="2">Recherche</option>
                <option value="3">À vendre</option>
                <option value="4">À donner</option>
                <option value="5">Service offert</option>
                <option value="6">Autre</option>
            </select>
        </div>

        <div class="form-group">
            <label>Petite description:</label>
            <input type="text" class="form-control" name="tbPetiteDesc" required placeholder="Petite description">
        </div>

        <div class="form-group">
            <label>Description longue:</label>
            <textarea name="tbGrosseDesc" class="form-control" rows="5" required placeholder="Description longue"></textarea>
        </div>

        <div class="form-group">
            <label>Prix ($):</label>
            <input type="number" class="form-control" name="tbPrixAnnonce" required placeholder="Prix">
            <span class="input-group-text">$</span>
        </div>

        <div class="form-group">
            <label>Sélectionnez l'image de l'annonce:</label>
            <input type="file" name="fileToUpload" accept="image/*" required>
        </div>

        <div class="form-group">
            <label>Activé ?</label>
            <select name="Activation" class="form-control">
                <option value="1">Actif</option>
                <option value="2">Inactif</option>
            </select>
        </div>

        <input type="submit" value="Ajouter" class="btn btn-primary">
        <a href="index.php" class="btn btn-secondary">Annuler</a>
        <a href="dashboard.php" class="btn btn-info">Retour au Dashboard</a>
    </form>
</div>
</body>
</html>