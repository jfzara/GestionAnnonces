<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="styles.css"> <!-- Inclure le fichier CSS -->
</head>
<body>
<nav class="navbar">
    <a href="annonces.php" class="nav-item">Annonces</a>
    <a href="gestion_annonces.php" class="nav-item">Gestion de vos annonces</a>
    <a href="modifier_profil.php" class="nav-item">Modification du profil</a>
    <a href="Deconnexion.php" class="nav-item">Déconnexion</a>
</nav>


    

    <form id="formMAJProfile" action="modifier_profil.php" method="POST">
    <h1>Modifier votre profil</h1>
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" placeholder="Entrez votre email" readonly>

        <label for="statut">Statut :</label>
        <select id="statut" name="statut">
            <option value="">Sélectionnez votre statut</option>
            <option value="2">Cadre</option>
            <option value="3">Employé de soutien</option>
            <option value="4">Enseignant</option>
            <option value="5">Professionnel</option>
        </select>

        <label for="tbNoEmp">Numéro Emploi :</label>
        <input type="text" id="tbNoEmp" name="tbNoEmp" placeholder="Entrez votre numéro d'emploi">

        <label for="tbTelM">Téléphone Maison :</label>
        <input type="text" id="tbTelM" name="tbTelM" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx">
        <label for="cbTelMP">Privé ?</label>
        <input type="checkbox" id="cbTelMP" name="cbTelMP">

        <label for="tbTelT">Téléphone Bureau :</label>
        <input type="text" id="tbTelT" name="tbTelT" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx">
        <label for="tbTelTPoste">Poste :</label>
        <input type="text" id="tbTelTPoste" name="tbTelTPoste" pattern="[0-9]{4}" placeholder="xxxx">
        <label for="cbTelTP">Privé ?</label>
        <input type="checkbox" id="cbTelTP" name="cbTelTP">

        <label for="tbTelC">Téléphone Cellulaire :</label>
        <input type="text" id="tbTelC" name="tbTelC" pattern="\([0-9]{3}\) [0-9]{3}-[0-9]{4}" placeholder="(xxx) xxx-xxxx">
        <label for="cbTelCP">Privé ?</label>
        <input type="checkbox" id="cbTelCP" name="cbTelCP">

        <button type="submit" class="soumettre">Enregistrer</button>

    </form>


</body>
</html>