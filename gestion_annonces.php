<div class="divGestion">
    <div class="text-right mx-3 my-2">
        <a href="ajouter_annonce.php" class="btn btn-primary text-light">Ajouter</a>
    </div>

    <h2>Gestion des annonces</h2> <!-- Titre de la page -->

    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>No</th>
                <th>No Annonce</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Date de parution</th>
                <th>État</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <!-- Les lignes des annonces existantes seront ici -->
            <tr>
                <td>
                    <div class="overflow-hidden text-right imageSize">
                        <img alt="Image" src="photos-annonces\fauteuil-bureau.jpg" width="144" class="m-auto">
                    </div>
                </td>
                <td>1</td>
                <td>3</td>
                <td><a href="Annonce.php?id=3">Fauteuil</a></td>
                <td>Recherche</td>
                <td>50 $</td>
                <td>2021-04-18 00:45:30</td>
                <td>Actif</td>
                <td><a href="modifier_annonce.php?id=3" class="btn btn-success">Modification</a></td>
                <td><a href="confirmation_retirer_annonce.php?id=3" class="btn btn-danger">Retrait</a></td>
                <td><a href="desactiver_action.php?id=3" class="btn btn-secondary">Désactiver</a></td>
            </tr>
            <!-- Ajoute d'autres lignes d'annonces ici -->
        </tbody>
    </table>
</div>