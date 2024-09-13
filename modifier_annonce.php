<?php
// Inclure la connexion à la base de données
include 'db_connection.php';

// Vérifier si un ID d'annonce a été passé dans l'URL
if (isset($_GET['id'])) {
    $idAnnonce = $_GET['id'];
    
    // Requête pour récupérer les détails de l'annonce
    $query = "SELECT * FROM annonces WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idAnnonce);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Vérifier si l'annonce existe
    if ($result->num_rows > 0) {
        $annonce = $result->fetch_assoc();
    } else {
        echo "Annonce non trouvée.";
        exit();
    }
} else {
    echo "Aucun ID d'annonce fourni.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'annonce</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container col-md-5 jumbotron">
    <h2 class="text-center">Modifier l'annonce</h2>
    <form enctype="multipart/form-data" method="POST" action="EnvoieModifAnnonce.php?id=<?php echo $idAnnonce; ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="100000">

        <div class="form-group">
            <label>Catégorie</label>
            <select name="ddlCategories" class="form-control">
                <option value="0">Sélectionner une catégorie</option>
                <option value="1" <?php echo ($annonce['categorie'] == 1) ? 'selected' : ''; ?>>Location</option>
                <option value="2" <?php echo ($annonce['categorie'] == 2) ? 'selected' : ''; ?>>Recherche</option>
                <option value="3" <?php echo ($annonce['categorie'] == 3) ? 'selected' : ''; ?>>À vendre</option>
                <option value="4" <?php echo ($annonce['categorie'] == 4) ? 'selected' : ''; ?>>À donner</option>
                <option value="5" <?php echo ($annonce['categorie'] == 5) ? 'selected' : ''; ?>>Service offert</option>
                <option value="6" <?php echo ($annonce['categorie'] == 6) ? 'selected' : ''; ?>>Autre</option>
            </select>
        </div>

        <div class="form-group">
            <label>Petite description:</label>
            <input type="text" class="form-control" name="tbPetiteDesc" value="<?php echo htmlspecialchars($annonce['petite_description']); ?>">
        </div>

        <div class="form-group">
            <label>Description longue:</label>
            <textarea name="tbGrosseDesc" class="form-control" rows="5"><?php echo htmlspecialchars($annonce['longue_description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Prix :</label>
            <input type="text" class="form-control" name="tbPrixAnnonce" value="<?php echo htmlspecialchars($annonce['prix']); ?>">
        </div>

        <div class="form-group">
            <label>Sélectionnez l'image de l'annonce:</label>
            <input type="file" name="fileToUpload" accept="image/*">
        </div>

        <div class="form-group">
            <label>Activé ?</label>
            <select name="Activation" class="form-control">
                <option value="1" <?php echo ($annonce['active'] == 1) ? 'selected' : ''; ?>>Actif</option>
                <option value="2" <?php echo ($annonce['active'] == 2) ? 'selected' : ''; ?>>Inactif</option>
            </select>
        </div>

        <input type="submit" value="Modifier" class="btn btn-primary">
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>