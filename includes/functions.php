<?php
/**
 * Fichier contentant les functions utilitaires communes pour le site.
 */

/**
 * Prendre en parametre le descriptif d'une offre et retourne un string formatté
 * @param mixed $descriptif descriptif non formatté
 * @return string descriptif formatté
 */
function formatterDescriptif($descriptif)
{
    if (empty(trim($descriptif))) {
        return '';
    }
    $formate = nl2br(htmlspecialchars($descriptif));

    $formate = str_replace('VOS MISSIONS :', '<h4 class="title--4">VOS MISSIONS :</h4>', $formate);
    $formate = str_replace('PROFIL ET CONDITIONS DE TRAVAIL :', '<h4 class="title--4">PROFIL ET CONDITIONS DE TRAVAIL :</h4>', $formate);
    $formate = str_replace('CANDIDATURE :', '<h4 class="title--4">CANDIDATURE :</h4>', $formate);
    $formate = trim(preg_replace('/(<br\s*\/?>\s*|^)>\s*/', '$1• ', $formate));
    return $formate;
}
/**
 * Verifie qu'un champ n'est pas vide
 * @param mixed $champ
 * @return bool vrai si rempli, faux si vide
 */
function estRempli($champ)
{
    return !empty(trim($champ));
}

/**
 * Filtre un array d'arrays contentant toutes les offres
 * @param array $toutes_offres
 * @return array array d'array d'offres qui correspondent aux filtres
 */
function filtrerOffres(array $toutes_offres)
{
    $offres_filtrees = array_filter($toutes_offres["offres"], function ($offre) {
        $contrat = true;
        $profession = true;
        $evenement = true;
        $motcle = true;
        $commune = true;
        $hebergement = true;
        $duree = true;
        // Selon type de contrat
        if (isset($_GET["contrat"]) and $_GET["contrat"] !== "" and $_GET["contrat"] !== "tous") {
            $contrat = (stripos($offre["CONTRAT"], $_GET["contrat"]) !== false);
        }
        // Selon titre d'annonce
        if (isset($_GET["profession"]) and $_GET["profession"] !== "" and $_GET["profession"] !== "tous") {
            $profession = ($offre["PROFESSION"] === $_GET["profession"]);
        }
        // Selon durée (match exacte seulement, elimine aussi durée non précisé)
        if (isset($_GET["duree"]) and $_GET["duree"] !== "" and $_GET["duree"] !== "tous") {
            $duree = (stripos($offre["DUREE"], $_GET["duree"]) !== false);
        }
        // PAS DE FILTRE POUR LE MOMENT - PAS DANS LES DONNEES
        if (isset($_GET["evenement"]) and $_GET["evenement"] !== "" and $_GET["evenement"] !== "tous") {
        }
        // Mot clé dans CERTAINS CHAMPS SEULEMENT
        if (isset($_GET["mot-cle"]) and $_GET["mot-cle"] !== "") {
            $motcle = (
                (stripos($offre["PROFESSION"], $_GET["mot-cle"]) !== false) or
                (stripos($offre["DESCRIPTIF"], $_GET["mot-cle"]) !== false) or
                (stripos($offre["LIEU"], $_GET["mot-cle"]) !== false) or
                (stripos($offre["HORAIRES"], $_GET["mot-cle"]) !== false) or
                (stripos($offre["SALAIRE"], $_GET["mot-cle"]) !== false));
        }
        // Selon commune
        if (
            isset($_GET["epine"]) || isset($_GET["noirmoutier"]) || isset($_GET["gueriniere"]) || isset($_GET["barbatre"])
        ) {
            $commune = false;
            if (isset($_GET["epine"]) && (stripos($offre["LIEU"], "L Epine"))) {
                $commune = true;
            } elseif (isset($_GET["noirmoutier"]) && $offre["LIEU"] === "Noirmoutier en l île") {
                $commune = true;
            } elseif (isset($_GET["gueriniere"]) && $offre["LIEU"] === "La Guérinière") {
                $commune = true;
            } elseif (isset($_GET["barbatre"]) && $offre["LIEU"] === "Barbâtre") {
                $commune = true;
            }
        }
        // PAS DE FILTRE POUR LE MOMENT - PAS DANS LES DONNEES
        if (isset($_GET["hebergement"])) {
        }
        return $contrat && $profession && $evenement && $motcle && $commune && $hebergement && $duree;
    });
    return $offres_filtrees;
}

/**
 * Compte les offres dans un array
 * @param array $offres_filtrees
 * @return string string avec compte
 */
function afficherCompteOffres(array $offres_filtrees)
{
    $compteur = count($offres_filtrees);
    if ($compteur > 1) {
        return "$compteur offres trouvées";
    }
    if ($compteur === 1) {
        return "1 offre trouvée";
    }
    return "Pas d'offre trouvée";
}

/**
 * Calcule le nombre de jours depuis la mise à jour d'une offre
 * @param array $offre
 * @return string text à afficher
 */
function afficherEcartTemps(array $offre)
{
    $date = DateTime::createFromFormat('d/m/Y', $offre['DATE_ACTU']);
    $now = new DateTime();
    $ecart = $now->diff($date);
    if ($ecart->days === 0) {
        $diff_string = "aujourd'hui";
    } elseif ($ecart->days === 1) {
        $diff_string = "hier";
    } else {
        $diff_string = "il y a " . $ecart->days . " jours";
    }
    return $diff_string;
}

/**
 * Genère un string content des elements <option> pour chaque profession unique trouvé.
 * @param array $toutes_offres les offres avant filtrage
 * @return string string de html à afficher
 */
function remplirSelectProfessionsUniques(array $toutes_offres)
{
    $professions = [];
    foreach ($toutes_offres["offres"] as $offre) {
        $professions[] = $offre["PROFESSION"];
    }
    $professions = array_unique($professions);
    $string_options_html = "";
    foreach ($professions as $profession) {
        $string_options_html .= "<option value=\"$profession\">$profession</option>";
    }
    return $string_options_html;
}