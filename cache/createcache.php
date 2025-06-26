<?php
include dirname(__DIR__).'/includes/config.php';
include dirname(__DIR__).'/includes/functions.php';
include dirname(__DIR__).'/includes/dbaccess.php';

// Chemin du fichier cache
$cacheOffres = __DIR__ . '/toutesOffres.cache';
$cacheProfessions = __DIR__ . '/professionsUniques.cache';

$dbaccess = new Dbaccess();
// Obtention de toutes les offres d'emploi du JSON
$toutes_offres = $dbaccess->getToutesOffres();

// Calculer l'age des offres
foreach ($toutes_offres as &$offre) {
    $offre['ageJours'] = calculerAgeJours($offre);
}
unset($offre);

// Trier les offres du plus récent au plus ancien (ageJours croissant)
usort($toutes_offres, function ($a, $b) {
    return $a['ageJours'] <=> $b['ageJours'];
});

// Creer representation html pour la liste d'offres
creerHtmlListe($toutes_offres);


// Les professions uninques
$professions_uniques = getSelectProfessionsUniques($toutes_offres);

// --- Caching ---

// Encode the filtered and sorted data using PHP serialization
$serializedOffres = serialize($toutes_offres);
// Check if encoding was successful (serialize rarely fails unless memory runs out)
if ($serializedOffres === false) {
    die('Error serializing data.');
}
// Write the serialized data to the cache file
if (file_put_contents($cacheOffres, $serializedOffres) === false) {
    die('Error writing cache file: ' . error_get_last()['message']);
}
echo "Cache file created successfully at: " . $cacheOffres;


// Encode the filtered and sorted data using PHP serialization
$serializedProfessions = serialize($professions_uniques);
// Check if encoding was successful (serialize rarely fails unless memory runs out)
if ($serializedProfessions === false) {
    die('Error serializing data.');
}
// Write the serialized data to the cache file
if (file_put_contents($cacheProfessions, $serializedProfessions) === false) {
    die('Error writing cache file: ' . error_get_last()['message']);
}
echo "Cache file created successfully at: " . $cacheProfessions;
?>