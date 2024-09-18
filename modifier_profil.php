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
        const NoEmpl = document.getElementById('tbNoEmpl').value.trim();
        const statut = document.getElementById('tbStatut').value;

        // Validation du nom
        if (nom === "") {
            errors.push("Le nom est obligatoire.");
        } else if (nom.length > 25) {
            errors.push("Le nom ne doit pas dépasser 25 caractères.");
        }

        // Validation du prénom
        if (prenom === "") {
            errors.push("Le prénom est obligatoire.");
        } else if (prenom.length > 50) {
            errors.push("Le prénom ne doit pas dépasser 50 caractères.");
        }

        // Validation de l'email
        if (email === "") {
            errors.push("L'email est obligatoire.");
        } else if (!validateEmail(email)) {
            errors.push("L'email n'est pas valide.");
        } else if (email.length > 50) {
            errors.push("L'email ne doit pas dépasser 50 caractères.");
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
            alert(errors.join("\n"));
            return false; // Ne pas soumettre le formulaire
        }

        // Formatage des numéros de téléphone
        document.getElementById('tbTelM').value = formatPhoneNumber(telMaison);
        document.getElementById('tbTelC').value = formatPhoneNumber(telCellulaire);
        document.getElementById('tbTelT').value = formatPhoneNumber(telTravail);

        // Soumettre le formulaire
        document.getElementById('formMAJProfile').submit();
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function formatPhoneNumber(phone) {
        // Supprime tout sauf les chiffres
        const digits = phone.replace(/\D/g, '');
        // Formate le numéro en (XXX) XXX-XXXX
        if (digits.length === 10) {
            return `(${digits.substring(0, 3)}) ${digits.substring(3, 6)}-${digits.substring(6)}`;
        } else {
            return phone; // Retourne le numéro tel quel s'il n'a pas 10 chiffres
        }
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
    <form id="formMAJProfile" action="envoi_maj_profil.php" method="POST" onsubmit="return validerFormulaire(event);">
        <!-- Champ Courriel -->
        <div class="form-group row">
            <label class="col-4 col-form-label" for="tbEmail">Courriel</label>
            <div class="col-6">
                <input type="email" class="form-control" id="tbEmail" name="courriel" placeholder="Entrez votre courriel" required value="<?php echo isset($courriel) ? htmlspecialchars($courriel) : ''; ?>">
            </div>
        </div>

        <!-- Lien pour modifier le mot de passe -->
        <div class="form-group row">
            <label for="tbMdp" class="col-4 col-form-label">Nouveau mot de passe</label>
            <div class="col-6">
                <a href="modifier_mdp.php">Accédez à la modification de mot de passe ici</a>
            </div>
        </div>

        <!-- Champ Statut -->
        <div class="form-group row">
            <label for="tbStatut" class="col-4 col-form-label">Statut</label>
            <div class="col-6">
                <select class="form-control" id="tbStatut" name="statut" required>
                    <option value="">Sélectionnez un statut</option>
                    <option value="1" <?php echo isset($statut) && $statut == 1 ? 'selected' : ''; ?>>Administrateur</option>
                    <option value="2" <?php echo isset($statut) && $statut == 2 ? 'selected' : ''; ?>>Cadre</option>
                    <option value="3" <?php echo isset($statut) && $statut == 3 ? 'selected' : ''; ?>>Employé de soutien</option>
                    <option value="4" <?php echo isset($statut) && $statut == 4 ? 'selected' : ''; ?>>Enseignant</option>
                    <option value="5" <?php echo isset($statut) && $statut == 5 ? 'selected' : ''; ?>>Professionnel</option>
                </select>
            </div>
        </div>

        <!-- Champ Numéro Employé -->
        <div class="form-group row">
            <label for="tbNoEmpl" class="col-4 col-form-label">Numéro Employé</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbNoEmpl" name="NoEmpl" placeholder="Entrez votre numéro d'emploi" required pattern="[0-9]+" title="Le numéro d'emploi doit être un nombre entier." value="<?php echo isset($NoEmpl) ? htmlspecialchars($NoEmpl) : ''; ?>">
            </div>
        </div>

        <!-- Champ Nom -->
        <div class="form-group row">
            <label for="tbNom" class="col-4 col-form-label">Nom</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbNom" name="nom" placeholder="Entrez votre nom" required maxlength="25" value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>">
            </div>
        </div>

        <!-- Champ Prénom -->
        <div class="form-group row">
            <label for="tbPrenom" class="col-4 col-form-label">Prénom</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbPrenom" name="prenom" placeholder="Entrez votre prénom" required maxlength="50" value="<?php echo isset($prenom) ? htmlspecialchars($prenom) : ''; ?>">
            </div>
        </div>

        <!-- Champ Numéro Téléphone Bureau -->
        <div class="form-group row">
            <label for="tbTelT" class="col-4 col-form-label">Numéro Téléphone Bureau</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelT" name="NoTelTravail" placeholder="Entrez votre téléphone bureau" value="<?php echo isset($NoTelTravail) ? htmlspecialchars($NoTelTravail) : ''; ?>">
            </div>
        </div>

        <!-- Champ Numéro Téléphone Cellulaire -->
        <div class="form-group row">
            <label for="tbTelC" class="col-4 col-form-label">Numéro Téléphone Cellulaire</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelC" name="NoTelCellulaire" placeholder="Entrez votre téléphone cellulaire" value="<?php echo isset($NoTelCellulaire) ? htmlspecialchars($NoTelCellulaire) : ''; ?>">
            </div>
        </div>

        <!-- Champ Numéro Téléphone Maison -->
        <div class="form-group row">
            <label for="tbTelM" class="col-4 col-form-label">Numéro Téléphone Maison</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelM" name="NoTelMaison" placeholder="Entrez votre téléphone maison" value="<?php echo isset($NoTelMaison) ? htmlspecialchars($NoTelMaison) : ''; ?>">
            </div>
        </div>

        <!-- Bouton Soumettre -->
        <div class="form-group row">
            <div class="col-6 offset-4">
                <button type="submit" class="btn btn-primary">Soumettre</button>
            </div>
        </div>
    </form>
</div>
</body>
</html>