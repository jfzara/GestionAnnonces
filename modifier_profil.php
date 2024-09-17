<?php
// Inclure le fichier de connexion à la base de données
include('db.php');

// Vérifier si les données ont été envoyées
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Récupération et nettoyage des données du formulaire
$nom = isset($_POST['tbNom']) ? trim($_POST['tbNom']) : '';
$prenom = isset($_POST['tbPrenom']) ? trim($_POST['tbPrenom']) : '';
$email = isset($_POST['tbEmail']) ? trim($_POST['tbEmail']) : '';
$telMaison = isset($_POST['tbTelM']) ? trim($_POST['tbTelM']) : '';
$telCellulaire = isset($_POST['tbTelC']) ? trim($_POST['tbTelC']) : '';
$posteBureau = isset($_POST['tbTelTPoste']) ? trim($_POST['tbTelTPoste']) : '';
$noEmp = isset($_POST['tbNoEmpl']) ? trim($_POST['tbNoEmpl']) : '';  // Corrigé ici
$statut = isset($_POST['tbStatut']) ? trim($_POST['tbStatut']) : '';

// Afficher les valeurs pour débogage
echo "Nom: $nom<br>";
echo "Prénom: $prenom<br>";
echo "Email: $email<br>";
echo "Téléphone Maison: $telMaison<br>";
echo "Téléphone Cellulaire: $telCellulaire<br>";
echo "Poste Bureau: $posteBureau<br>";
echo "Numéro Employé: $noEmp<br>";
echo "Statut: $statut<br>";


// Préparer la requête SQL
$sql = "UPDATE utilisateurs SET 
    Nom = ?, 
    Prenom = ?, 
    Courriel = ?, 
    NoTelMaison = ?, 
    NoTelCellulaire = ?, 
    NoTelTravail = ?, 
    Statut = ? 
WHERE NoEmpl = ?";

    // Préparer la requête
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Erreur lors de la préparation de la requête: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("sssssssi", $nom, $prenom, $email, $telMaison, $telCellulaire, $posteBureau, $statut, $noEmp);

    // Exécuter la requête
    if ($stmt->execute()) {
        echo "Mise à jour réussie!";
    } else {
        echo "Erreur lors de la mise à jour: " . $stmt->error;
    }

    // Fermer la déclaration et la connexion
    $stmt->close();
    $conn->close();
} else {
    echo "Erreur : Méthode de requête invalide.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function validerFormulaire(event) {
            event.preventDefault(); // Empêcher la soumission du formulaire par défaut
            let errors = [];
            const nom = document.getElementById('tbNom').value.trim();
            const prenom = document.getElementById('tbPrenom').value.trim();
            const email = document.getElementById('tbEmail').value.trim();
            const tbTelM = document.getElementById('tbTelM').value.trim();
            const tbTelC = document.getElementById('tbTelC').value.trim();
            const posteTelBureau = document.getElementById('tbTelTPoste').value.trim();
            const NoEmpl = document.getElementById('tbNoEmpl').value.trim();
            const statut = document.getElementById('tbStatut').value;

            // Validation des champs
            console.log("Validation des champs");
            console.log("Nom:", nom);
            console.log("Prénom:", prenom);
            console.log("Email:", email);
            console.log("Téléphone Maison:", tbTelM);
            console.log("Téléphone Cellulaire:", tbTelC);
            console.log("Numéro Poste Bureau:", posteTelBureau);
            console.log("Numéro Employé:", NoEmpl);
            console.log("Statut:", statut);

            if (nom === "") {
                errors.push("Le nom est obligatoire.");
            } else if (nom.length > 50) {
                errors.push("Le nom ne doit pas dépasser 50 caractères.");
            }
            
            if (prenom === "") {
                errors.push("Le prénom est obligatoire.");
            } else if (prenom.length > 50) {
                errors.push("Le prénom ne doit pas dépasser 50 caractères.");
            }
            
            if (email === "") {
                errors.push("L'email est obligatoire.");
            } else if (!validateEmail(email)) {
                errors.push("L'email n'est pas valide.");
            }

            const telMaisonPattern = /^\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/;
            if (tbTelM !== "" && !telMaisonPattern.test(tbTelM)) {
                errors.push("Le téléphone maison doit être au format (xxx) xxx-xxxx ou xxx-xxx-xxxx ou xxxxxxxxxx.");
            }

            if (tbTelC !== "" && !telMaisonPattern.test(tbTelC)) {
                errors.push("Le téléphone cellulaire doit être au format (xxx) xxx-xxxx ou xxx-xxx-xxxx ou xxxxxxxxxx.");
            }

            if (posteTelBureau === "") {
                errors.push("Le numéro de poste est obligatoire.");
            } else if (!/^[0-9]{4}$/.test(posteTelBureau)) {
                errors.push("Le numéro de poste doit contenir 4 chiffres.");
            }

            if (NoEmpl === "") {
                errors.push("Le numéro d'emploi est obligatoire.");
            } else if (!/^[0-9]+$/.test(NoEmpl)) {
                errors.push("Le numéro d'emploi doit être un nombre entier.");
            }

            if (statut === "") {
                errors.push("Le statut est obligatoire.");
            }

            // Affichage des erreurs
            if (errors.length > 0) {
                console.log("Erreurs trouvées:", errors);
                alert(errors.join("\n"));
                return false; // Ne pas soumettre le formulaire
            }

            console.log("Aucune erreur trouvée, soumission du formulaire.");
            // Si tout est bon, soumettre le formulaire
            document.getElementById('formMAJProfile').submit();
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Formater le numéro de téléphone
        function formatTelephone(input) {
            const digits = input.value.replace(/\D/g, '');
            let formattedNumber = '';

            if (digits.length <= 3) {
                formattedNumber = digits;
            } else if (digits.length <= 6) {
                formattedNumber = (${digits.slice(0, 3)}) ${digits.slice(3)};
            } else {
                formattedNumber = (${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6, 10)};
            }

            input.value = formattedNumber;
        }
    </script>
</head>
<body>
<nav class="navbar">
    <a href="annonces.php" class="nav-item">Annonces</a>
    <a href="gestion_annonces.php" class="nav-item">Gestion de vos annonces</a>
    <a href="modifier_profil.php" class="nav-item">Modification du profil</a>
    <a href="logout.php" class="nav-item">Déconnexion</a>
</nav>

<div id="divMAJProfile" class="form-container">
    <h1 id="titreMAJProfile">Modifier votre profil</h1>
    <br>
    <form id="formMAJProfile" action="envoi_maj_profil.php" method="POST" onsubmit="validerFormulaire(event);">

        <div class="form-group row">
            <label class="col-4 col-form-label" for="tbEmail">Email</label>
            <div class="col-6">
                <input type="email" class="form-control" id="tbEmail" name="email" placeholder="Entrez votre email" required>
            </div>
            <p id="errEmail" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbMdp" class="col-4 col-form-label">Nouveau mot de passe</label>
            <div class="col-6">
                <a href="modifier_mdp.php">Accédez à la modification de mot de passe ici</a>
            </div>
            <p id="errMdp" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbStatut" class="col-4 col-form-label">Statut</label>
            <div class="col-6">
                <select class="form-control" id="tbStatut" name="statut">
                    <option value="0">En attente</option>
                    <option value="9">Confirmé</option>
                    <option value="1">Administrateur</option>
                    <option value="2">Cadre</option>
                    <option value="3">Employé de soutien</option>
                    <option value="4" selected>Enseignant</option>
                    <option value="5">Professionnel</option>
                </select>
            </div>
            <p id="errStatut" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbNoEmpl" class="col-4 col-form-label">Numéro Employé</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbNoEmpl" name="NoEmpl" placeholder="Entrez votre numéro d'emploi" required pattern="[0-9]+" title="Le numéro d'emploi doit être un nombre entier.">
            </div>
            <p id="errNoEmpl" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbNom" class="col-4 col-form-label">Nom</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbNom" name="nom" placeholder="Entrez votre nom" required maxlength="50">
            </div>
            <p id="errNom" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbPrenom" class="col-4 col-form-label">Prénom</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbPrenom" name="prenom" placeholder="Entrez votre prénom" required maxlength="50">
            </div>
            <p id="errPrenom" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbTelT" class="col-4 col-form-label">Numéro Téléphone Bureau</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelT" name="NoTelBureau" oninput="formatTelephone(this)" placeholder="(xxx) xxx-xxxx">
                <div class="col row mt-3">
                    <label for="tbTelTPoste" class="col-4 col-form-label">Poste</label>
                    <input type="text" class="col-4 form-control" id="tbTelTPoste" name="PosteBureau" pattern="[0-9]{4}" placeholder="xxxx" title="Le numéro de poste doit contenir 4 chiffres.">
                </div>
            </div>
            <p id="errTelT" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbTelM" class="col-4 col-form-label">Téléphone Maison</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelM" name="NoTelMaison" oninput="formatTelephone(this)" placeholder="(xxx) xxx-xxxx">
            </div>
            <p id="errTelM" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbTelC" class="col-4 col-form-label">Téléphone Cellulaire</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelC" name="NoTelCellulaire" oninput="formatTelephone(this)" placeholder="(xxx) xxx-xxxx">
            </div>
            <p id="errTelC" class="text-danger font-weight-bold"></p>
        </div>

        <button type="submit" class="btn btn-primary">Soumettre</button>
    </form>
</div>