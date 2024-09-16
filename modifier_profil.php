
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function validerFormulaire() {
            let errors = [];
            const nom = document.getElementById('tbNom').value.trim();
            const prenom = document.getElementById('tbPrenom').value.trim();
            const email = document.getElementById('tbEmail').value.trim();
            const tbTelM = document.getElementById('tbTelM').value.trim();
            const tbTelC = document.getElementById('tbTelC').value.trim();
            const posteTelBureau = document.getElementById('tbTelTPoste').value.trim();

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

            const telMaisonPattern = /^\([0-9]{3}\) [0-9]{3}-[0-9]{4}$/;
            if (tbTelM !== "" && !telMaisonPattern.test(tbTelM)) {
                errors.push("Le téléphone maison doit être au format (xxx) xxx-xxxx.");
            }

            if (tbTelC !== "" && !telMaisonPattern.test(tbTelC)) {
                errors.push("Le téléphone cellulaire doit être au format (xxx) xxx-xxxx.");
            }

            if (posteTelBureau === "") {
                errors.push("Le numéro de poste est obligatoire.");
            }

            if (errors.length > 0) {
                alert(errors.join("\n"));
                return false;
            }

            return true;
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
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
    <h1 id="titreMAJProfile">Mise à jour du profil</h1>
    <br>
    <form id="formMAJProfile" action="EnvoieMAJProfile.php" method="POST">

        <div class="form-group row">
            <label class="col-4 col-form-label" for="tbEmail">Email</label>
            <div class="col-6">
                <input type="text" readonly class="form-control" id="tbEmail" name="tbEmail" placeholder="Entrez votre email">
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
                <select class="form-control" id="tbStatut" name="tbStatut">
                    <option value="2">Cadre</option>
                    <option value="3">Employé de soutien</option>
                    <option value="4" selected>Enseignant</option>
                    <option value="5">Professionnel</option>
                </select>
            </div>
            <p id="errStatut" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbNoEmp" class="col-4 col-form-label">Numéro Emplois</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbNoEmp" name="tbNoEmp" placeholder="Entrez votre numéro d'emploi">
            </div>
            <p id="errNoEmp" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbNom" class="col-4 col-form-label">Nom</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbNom" name="tbNom" placeholder="Entrez votre nom" required>
            </div>
            <p id="errNom" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbPrenom" class="col-4 col-form-label">Prénom</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbPrenom" name="tbPrenom" placeholder="Entrez votre prénom" required>
            </div>
            <p id="errPrenom" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbTelT" class="col-4 col-form-label">Numéro Téléphone Bureau</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelT" name="tbTelT" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx">
                <div class="col row mt-3">
                    <label for="tbTelTPoste" class="col-4 col-form-label">Poste</label>
                    <input type="text" class="col-4 form-control" id="tbTelTPoste" name="tbTelTPoste" pattern="[0-9]{4}" placeholder="xxxx">
                </div>
                <label for="cbTelTP" class="col-5 col-form-label">Privé ?</label>
                <input type="checkbox" class="" id="cbTelTP" name="cbTelTP">
            </div>
            <p id="errTelT" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbTelM" class="col-4 col-form-label">Numéro Téléphone Maison</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelM" name="tbTelM" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx">
                <label for="cbTelMP" class="col-5 col-form-label">Privé ?</label>
                <input type="checkbox" class="" id="cbTelMP" name="cbTelMP" checked>
            </div>
            <p id="errTelM" class="text-danger font-weight-bold"></p>
        </div>

        <div class="form-group row">
            <label for="tbTelC" class="col-4 col-form-label">Numéro Téléphone Cellulaire</label>
            <div class="col-6">
                <input type="text" class="form-control" id="tbTelC" name="tbTelC" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx">
                <label for="cbTelCP" class="col-5 col-form-label">Privé ?</label>
                <input type="checkbox" class="" id="cbTelCP" name="cbTelCP" checked>
            </div>
            <p id="errTelC" class="text-danger font-weight-bold"></p>
        </div>

        <p class="text-danger"></p>
        <div class="d-flex">
            <button type="submit" class="btn btn-primary" id="btnMAJProfile">Enregistrer</button>
        </div>
    </form>
</div>
</body>
</html>