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

// --- CACHING toutes_offres ---
$serializedOffres = serialize($toutes_offres);
if ($serializedOffres === false) {
    die('Error serializing data.');
}
if (file_put_contents($cacheOffres, $serializedOffres) === false) {
    die('Error writing cache file: ' . error_get_last()['message']);
}
echo "Cache file created successfully at: " . $cacheOffres;

// --- CACHING profession_uniques ---
$serializedProfessions = serialize($professions_uniques);
if ($serializedProfessions === false) {
    die('Error serializing data.');
}
if (file_put_contents($cacheProfessions, $serializedProfessions) === false) {
    die('Error writing cache file: ' . error_get_last()['message']);
}
echo "Cache file created successfully at: " . $cacheProfessions;
?>