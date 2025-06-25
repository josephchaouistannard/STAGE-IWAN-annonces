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
 * @param array $params array de parametres nettoyés et validés
 * @return array array d'array d'offres qui correspondent aux filtres
 */
function filtrerOffres(array $toutes_offres, array $params)
{

    // Filtrage de toutes offres selon parametres
    $offres_filtrees = array_filter($toutes_offres["offres"], function ($offre) use ($params) {
        $contrat = true;
        $profession = true;
        $evenement = true;
        $motcle = true;
        $commune = true;
        $hebergement = true;
        $duree = true;
        // Selon type de contrat
        if (isset($params["contrat"]) and $params["contrat"] !== "" and $params["contrat"] !== "tous") {
            $contrat = (stripos($offre["CONTRAT"], $params["contrat"]) !== false);
        }
        // Selon titre d'annonce
        if (isset($params["profession"]) and $params["profession"] !== "" and $params["profession"] !== "tous") {
            $profession = ($offre["PROFESSION"] === $params["profession"]);
        }
        // Selon durée (match exacte seulement, elimine aussi durée non précisé)
        if (isset($params["duree"]) and $params["duree"] !== "" and $params["duree"] !== "tous") {
            $duree = (stripos($offre["DUREE"], $params["duree"]) !== false);
        }
        // PAS DE FILTRE POUR LE MOMENT - PAS DANS LES DONNEES
        // if (isset($params["evenement"]) and $params["evenement"] !== "" and $params["evenement"] !== "tous") {
        // }
        // Mot clé dans CERTAINS CHAMPS SEULEMENT
        if (isset($params["mot-cle"]) and $params["mot-cle"] !== "") {
            $motcle = (
                (stripos($offre["PROFESSION"], $params["mot-cle"]) !== false) or
                (stripos($offre["DESCRIPTIF"], $params["mot-cle"]) !== false) or
                (stripos($offre["LIEU"], $params["mot-cle"]) !== false) or
                (stripos($offre["HORAIRES"], $params["mot-cle"]) !== false) or
                (stripos($offre["SALAIRE"], $params["mot-cle"]) !== false));
        }
        // Selon commune
        if (
            $params["epine"] || $params["noirmoutier"] || $params["gueriniere"] || $params["barbatre"]
        ) {           $commune = false;
            if ($params["epine"] && stripos($offre["LIEU"], "L EPINE") !== false) {
                $commune = true;
            } elseif ($params["noirmoutier"] && stripos($offre["LIEU"], "NOIRMOUTIER EN L ILE") !== false) {
                $commune = true;
            } elseif ($params["gueriniere"] && stripos($offre["LIEU"], "LA GUERINIERE") !== false) {
                $commune = true;
            } elseif ($params["barbatre"] && stripos($offre["LIEU"], "BARBATRE") !== false) {
                $commune = true;
            }
        }
        // PAS DE FILTRE POUR LE MOMENT - PAS DANS LES DONNEES
        // if (isset($params["hebergement"])) {
        // }
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

/**
 * Valide et nettoie les paramètres de filtre reçus via la requête GET.
 *
 * Retourne un tableau associatif des paramètres validés et nettoyés.
 * Les valeurs par défaut sont utilisées si un paramètre est manquant ou invalide.
 *
 * @return array Un tableau associatif des paramètres de filtre validés.
 */
function validerParamsFiltrage()
{
    $params = [];

    // Lire les valeurs GET ou appliquer par défaut
    $params['contrat'] = $_GET['contrat'] ?? 'tous';
    $params['profession'] = $_GET['profession'] ?? 'tous';
    $params['duree'] = $_GET['duree'] ?? 'tous';
    $params['evenement'] = $_GET['evenement'] ?? 'tous';
    $params['mot-cle'] = $_GET['mot-cle'] ?? '';
    $params['epine'] = isset($_GET['epine']);
    $params['noirmoutier'] = isset($_GET['noirmoutier']);
    $params['gueriniere'] = isset($_GET['gueriniere']);
    $params['barbatre'] = isset($_GET['barbatre']);
    $params['hebergement'] = isset($_GET['hebergement']);

    // Nettoyer les valeurs
    $params['contrat'] = htmlspecialchars(trim($params['contrat']));
    $params['profession'] = htmlspecialchars(trim($params['profession']));
    $params['duree'] = htmlspecialchars(trim($params['duree']));
    $params['evenement'] = htmlspecialchars(trim($params['evenement']));
    $params['mot-cle'] = htmlspecialchars(trim($params['mot-cle']));

    return $params;
}

/**
 * Valide le GET parametre NUMOFFRE
 * @return string NUMOFFRE validé
 */
function validerParamNumOffre() {
    return htmlspecialchars(trim($_GET['NUMOFFRE']));
}