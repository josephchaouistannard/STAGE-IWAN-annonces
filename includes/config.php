<?php
// Parametres d'affichage d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Duree de vie de cache en secondes (e.g., 5 hours = 18000 seconds)
$ageMaxCache = 18000;
// Chemins des fichiers cache
$cacheProfessions = __DIR__ . '/../cache/professionUniques.cache';
$cacheOffres = __DIR__ . '/../cache/toutesOffres.cache';


// CONTENU A CUSTOMISER
$header_logo = "<img id=\"header-img\" src=\"assets/logo.svg\" width=\"120\" height=\"60\">";
$header_text = "<h1>Maison de l'emploi Île de Noirmoutier</h1>";

$footer_text_gauche = "<p><strong>Maison de l'Emploi - France services de l'île de Noirmoutier</strong></p>
                    <p>(Service de la Communauté de Communes)<br>
                    11 rue de la Prée-au-Duc<br>
                    85330 Noirmoutier en l’île<br>
                    02 51 39 32 18<br>
                    emploi@iledenoirmoutier.org<br>
                    <p><strong>Horaires d’ouverture</strong></p>
                    <p>Lundi, mardi, jeudi : 8h30 à 12h / 13h15 à 17h<br>
                    Mercredi et vendredi : 8h30 à 12h</p>";
$footer_img_droite = "<img id=\"footer-img\" src=\"assets/logo.svg\">";

// communes to add here, an array with correct names, and value from db. Need to change filter function, display checkboxes