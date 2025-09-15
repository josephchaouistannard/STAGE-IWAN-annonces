<?php
// Parametres d'affichage d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Duree de vie de cache en secondes (e.g., 5 hours = 18000 seconds)
$ageMaxCache = 18000;
// Chemins des fichiers cache
$cacheProfessions = __DIR__ . '/../cache/professionUniques.cache';
$cacheOffres = __DIR__ . '/../cache/toutesOffres.cache';
$cacheEvenements = __DIR__ . '/../cache/evenements.cache';
$cacheDurees = __DIR__ . '/../cache/durees.cache';
$cacheTypesDeContrat = __DIR__ . '/../cache/typesDeContrat.cache';


// CONTENU A CUSTOMISER
$chemin_db_min = dirname(__DIR__) . "/NOIRMOUTIER-PHP.json";
$chemin_db_maj = dirname(__DIR__) . "/NOIRMOUTIER-PHP.JSON";
// $chemin_db_min = dirname(__DIR__) . "/SAVENAY-PHP.json";
// $chemin_db_maj = dirname(__DIR__) . "/SAVENAY-PHP.JSON";

// Liste de communes dans format COMMUNE BDD => COMMUNE AFFICHAGE (pour filtres)
$groupes_geographiques = [
    "La Guérinière" => "La Guérinière",
    "Noirmoutier-en-l'île" => " Noirmoutier-en-l'île",
    "Barbâtre" => " Barbâtre",
    "L'Epine" => " L'Epine",
];
// $groupes_geographiques = [
// "Département" => "Département",
// "Comcom" => "Communauté de Communes",
// "Bassin Nazérien" => "Bassin Nazérien",
// "- 30 min" => "Moins de 30 minutes",
// ];

// Durées possible dans l'ordre
$durees_possibles = [
    "1 jour",
    "5 semaines",
    "1 mois",
    "1.5 mois",
    "2 mois",
    "2.5 mois",
    "3 mois",
    "3.5 mois",
    "4 mois",
    "4.5 mois",
    "5 mois",
    "5.5 mois",
    "6 mois",
    "6.5 mois",
    "7 mois",
    "8 mois",
    "A l'année",
    "CDI"
];

$index_page_titre = "Consultation des Offres";
$index_et_offre_page_description = "Consulter les offres d'emploi de l'île de Noirmoutier";


$header_logo = "<img id=\"header-img\" src=\"assets/logo.svg\" alt=\"Logo de la maison d'emploi de Noirmoutier\" width=\"120\" height=\"60\">";
$header_text = "<h1>Maison de l'Emploi</h1><h2>Ile de Noirmoutier</h2>";

$footer_text_gauche = "<p><strong>Maison de l'Emploi - France services de l'île de Noirmoutier</strong></p>
                    <p>11 rue de la Prée-au-Duc<br>
                    85330 Noirmoutier en l’île<br>
                    02 51 39 32 18<br>
                    emploi@iledenoirmoutier.org<br>
                    <p><strong>Horaires d’ouverture au public</strong></p>
                    <p>Lundi, mardi, jeudi : 8h30 à 12h / 13h15 à 16h30<br>
                    Mercredi et vendredi : 8h30 à 12h</p>
                    <p>🔗 <a href=\"https://www.cdc-iledenoirmoutier.com/emploi-et-evolution-professionnelle\" target=\"_blank\">Emploi et évolution professionnelle | Noirmoutier Communauté de Communes</a></p>";
$footer_img_droite = "<img id=\"footer-img\" src=\"assets/logo.svg\" alt=\"Logo de la maison d'emploi de Noirmoutier\">";

// communes to add here, an array with correct names, and value from db. Need to change filter function, display checkboxes