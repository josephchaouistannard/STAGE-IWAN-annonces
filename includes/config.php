<?php
// Parametres d'affichage d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Duree de vie de cache en secondes (e.g., 1 hour = 3600 seconds)
$ageMaxCache = 3600;
// Chemins des fichiers cache
$cacheProfessions = __DIR__ . '/../cache/professionUniques.cache';
$cacheOffres = __DIR__ . '/../cache/toutesOffres.cache';


// CONTENU A CUSTOMISER
$header_logo = "<img id=\"header-img\" src=\"assets/logo.svg\" width=\"120\" height=\"60\">";
$header_text = "<h1>Maison de l'emploi Île de Noirmoutier</h1>";

$footer_text_gauche = "<p><strong>Communauté de Communes</strong></p>
                    <p>51 rue de la Prée au Duc<br>85330 Noirmoutier en l’île<br>02 51 35 89 89</p>
                    <p><strong>Horaires d’ouverture</strong></p>
                    <p>Le lundi, mardi et jeudi de 9h00 à 12h30 et de 14h00 à 17H30<br>Le mercredi et vendredi de 9h00 à
                        12h30 (fermé l’après-midi)</p>";
$footer_img_droite = "<img id=\"footer-img\" src=\"assets/logo.svg\">";

// communes to add here, an array with correct names, and value from db. Need to change filter function, display checkboxes