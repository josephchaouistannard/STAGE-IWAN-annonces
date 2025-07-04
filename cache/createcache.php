<?php
/**
 * Script qui refraichi le cache des offres et des professions uniques
 */
require_once __DIR__ . "/../includes/functions.php";

// Obtention de toutes les offres d'emploi du JSON
$dbaccess = new Dbaccess();
$toutes_offres_non_traitees = $dbaccess->chargerToutesOffresJSON();
$toutes_offres = traiterOffresJSON($toutes_offres_non_traitees);

// Les professions uninques à partir des offres du JSON
$professions_uniques = creerHtmlProfessionsUniques($toutes_offres);

// Les evenements à partir du json
$evenements = creerHtmlEvenements($toutes_offres);

// --- CACHING toutes_offres ---
$serializedOffres = serialize($toutes_offres);
if ($serializedOffres === false) {
    die('Error serializing data.');
}
if (file_put_contents($cacheOffres, $serializedOffres) === false) {
    die('Error writing cache file: ' . error_get_last()['message'] . "<br><br>");
}
echo "Offres cache reussi: " . $cacheOffres . "<br><br>";

// --- CACHING profession_uniques ---
$serializedProfessions = serialize($professions_uniques);
if ($serializedProfessions === false) {
    die('Error serializing data.' . "<br><br>");
}
if (file_put_contents($cacheProfessions, $serializedProfessions) === false) {
    die('Error writing cache file: ' . error_get_last()['message'] . "<br><br>");
}
echo "Professions cache reussi: " . $cacheProfessions . "<br><br>";

// --- CACHING evenements ---
$serializedEvenements = serialize($evenements);
if ($serializedEvenements === false) {
    die('Error serializing data.' . "<br><br>");
}
if (file_put_contents($cacheEvenements, $serializedEvenements) === false) {
    die('Error writing cache file: ' . error_get_last()['message'] . "<br><br>");
}
echo "Professions cache reussi: " . $cacheEvenements . "<br><br>";

// NETTOYAGE DE COMPTEUR - Suppression des annonces qui n'existent plus
// Charger les vues actuelles
$compteur_vues = $dbaccess->chargerVues();

// Créer une liste des NumOffre toujours valides à partir de $toutes_offres
$valid_num_offres = [];
foreach ($toutes_offres as $offre) {
    if (isset($offre['NumOffre'])) {
        $valid_num_offres[] = $offre['NumOffre'];
    }
}

// Parcourir le compteur de vues et supprimer les entrées non valides
$nouveau_compteur_vues = [];
foreach ($compteur_vues as $num_offre => $vues) {
    // Check if the NumOffre from the counter exists in the list of valid offers
    if (in_array($num_offre, $valid_num_offres)) {
        $nouveau_compteur_vues[$num_offre] = $vues;
    }
}

// Enregistrer le compteur de vues nettoyé
$dbaccess->enregistrerVues($nouveau_compteur_vues);

echo "Compteur de vues nettoyé. Supprimé les entrées pour les offres inexistantes.<br><br>";

?>