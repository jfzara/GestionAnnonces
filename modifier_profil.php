<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    function validerFormulaire(event) {
     
        let errors = [];
        const nom = document.getElementById('tbNom').value.trim();
        const prenom = document.getElementById('tbPrenom').value.trim();
        const email = document.getElementById('tbEmail').value.trim();
        const telMaison = document.getElementById('tbTelM').value.trim();
        const telCellulaire = document.getElementById('tbTelC').value.trim();
        const telTravail = document.getElementById('tbTelT').value.trim();
        const NoEmpl = document.getElementById('tbNoEmpl').value.trim(); // Utilisation de trim()
        const statut = document.getElementById('tbStatut').value;

        // Validation des champs
        console.log("Validation des champs");
        console.log("Nom:", nom);
        console.log("Prénom:", prenom);
        console.log("Email:", email);
        console.log("Téléphone Maison:", telMaison);
        console.log("Téléphone Cellulaire:", telCellulaire);
        console.log("Téléphone Travail:", telTravail);
        console.log("Numéro Employé:", NoEmpl);
        console.log("Statut:", statut);

        // Validation du nom (max 25 caractères)
        if (nom === "") {
            errors.push("Le nom est obligatoire.");
        } else if (nom.length > 25) {
            errors.push("Le nom ne doit pas dépasser 25 caractères.");
        }

        // Validation du prénom (max 50 caractères)
        if (prenom === "") {
            errors.push("Le prénom est obligatoire.");
        } else if (prenom.length > 50) {
            errors.push("Le prénom ne doit pas dépasser 50 caractères.");
        }

        // Validation de l'email (max 50 caractères)
        if (email === "") {
            errors.push("L'email est obligatoire.");
        } else if (!validateEmail(email)) {
            errors.push("L'email n'est pas valide.");
        } else if (email.length > 50) {
            errors.push("L'email ne doit pas dépasser 50 caractères.");
        }

        // Validation des numéros de téléphone
        const telPattern = /^\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}$/;
        if (telMaison !== "" && !telPattern.test(telMaison)) {
            errors.push("Le téléphone maison doit être au format (xxx) xxx-xxxx ou xxx-xxx-xxxx ou xxxxxxxxxx.");
        } else if (telMaison.length > 15) {
            errors.push("Le téléphone maison ne doit pas dépasser 15 caractères.");
        }

        if (telCellulaire !== "" && !telPattern.test(telCellulaire)) {
            errors.push("Le téléphone cellulaire doit être au format (xxx) xxx-xxxx ou xxx-xxx-xxxx ou xxxxxxxxxx.");
        } else if (telCellulaire.length > 15) {
            errors.push("Le téléphone cellulaire ne doit pas dépasser 15 caractères.");
        }

        if (telTravail !== "" && !telPattern.test(telTravail)) {
            errors.push("Le téléphone travail doit être au format (xxx) xxx-xxxx ou xxx-xxx-xxxx ou xxxxxxxxxx.");
        } else if (telTravail.length > 21) {
            errors.push("Le téléphone travail ne doit pas dépasser 21 caractères.");
        }

        // Validation du numéro d'employé
        if (NoEmpl === "") {
            errors.push("Le numéro d'emploi est obligatoire.");
        } else if (!/^[0-9]+$/.test(NoEmpl)) {
            errors.push("Le numéro d'emploi doit être un nombre entier.");
        }

        // Validation du statut
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
         // Afficher les informations saisies dans une alerte
       

        
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
            formattedNumber = `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
        } else {
            formattedNumber = `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6, 10)}`;
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
                <input type="email" class="form-control" id="tbEmail" name="courriel" placeholder="Entrez votre email" required>
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
