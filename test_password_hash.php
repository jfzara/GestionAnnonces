<?php
// Test de génération d'un mot de passe haché
$nouveauMotDePasse = 'NouveauMdp123'; 
$hashedPassword = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);

// Affichage du mot de passe haché et de sa longueur
echo "Mot de passe haché : " . $hashedPassword . "<br>";
echo "Longueur du mot de passe haché : " . strlen($hashedPassword);
?>