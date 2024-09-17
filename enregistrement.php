<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ?>
</head>
<body>
    <div class="container col-md-5 jumbotron">
        <h2 class="text-center">Enregistrement</h2><br>
        <form id="formInscription" method="POST" action="modifier_profil.php">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Courriel</label>
                    <input type="email" class="form-control" id="tbinscriptionEmail" name="tbinscriptionEmail" placeholder="Courriel @" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Confirmer Courriel</label>
                    <input type="email" class="form-control" id="tbinscriptionEmailConfirm" name="tbinscriptionEmailConfirm" placeholder="Confirmer Courriel" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Mot de passe</label>
                    <input type="password" class="form-control" id="tbInscriptionMDP" name="tbInscriptionMDP" placeholder="Mot de Passe" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Confirmer Mot de passe</label>
                    <input type="password" class="form-control" id="tbInscriptionMDPConfirm" name="tbInscriptionMDPConfirm" placeholder="Confirmer Mot de Passe" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
            <p>Déjà Membre ? <a href="login.php">Connectez-vous ici</a>.</p>
        </form>
    </div>

    <script>
        document.getElementById('formInscription').addEventListener('submit', function(event) {
            var email = document.getElementById('tbinscriptionEmail').value;
            var emailConfirm = document.getElementById('tbinscriptionEmailConfirm').value;
            var password = document.getElementById('tbInscriptionMDP').value;
            var passwordConfirm = document.getElementById('tbInscriptionMDPConfirm').value;

            // Message d'alerte pour vérifier que le script est bien exécuté
            alert("Soumission du formulaire déclenchée");

            console.log("Vérification des champs...");  // Message de débogage dans la console

            // Vérification des correspondances des champs
            if (email !== emailConfirm) {
                alert("Les courriels ne correspondent pas.");
                event.preventDefault();  // Empêche l'envoi du formulaire si erreur
                return false;
            }

            if (password !== passwordConfirm) {
                alert("Les mots de passe ne correspondent pas.");
                event.preventDefault();  // Empêche l'envoi du formulaire si erreur
                return false;
            }

            console.log("Formulaire soumis avec succès.");
        });
    </script>
</body>
</html>

