<?php
/**
 * Fichier contentant les functions communes pour le site.
 */
session_start();
require_once __DIR__ . "/config.php"; // Paramêtres PHP comme affichage d'erreurs
require_once __DIR__ . "/dbaccess.php"; // Class d'accès aux données
$dbaccess = new Dbaccess();

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
    $offres_filtrees = array_filter($toutes_offres, function ($offre) use ($params) {
        $contrat = true;
        $profession = true;
        $evenement = true;
        $motcle = true;
        $commune = true;
        $hebergement = true;
        $duree = true;
        // Selon type de contrat
        if (isset($params["contrat"]) and $params["contrat"] !== "" and $params["contrat"] !== "tous") {
            $contrat = (stripos($offre["TypeContrat"], $params["contrat"]) !== false);
        }
        // Selon titre d'annonce
        if (isset($params["profession"]) and $params["profession"] !== "" and $params["profession"] !== "tous") {
            $profession = ($offre["LibPoste"] === $params["profession"]);
        }
        // Selon durée (match exacte seulement, elimine aussi durée non précisé)
        if (isset($params["duree"]) and $params["duree"] !== "" and $params["duree"] !== "tous") {
            $duree = (stripos($offre["DureeContrat"], $params["duree"]) !== false);
        }
        // Selon evenement
        if (isset($params["evenement"]) and $params["evenement"] !== "" and $params["evenement"] !== "tous") {
            $evenement = (trim($offre["EvenementOffre"]) == $params["evenement"]);
        }
        // Mot clé dans CERTAINS CHAMPS SEULEMENT
        if (isset($params["mot-cle"]) and $params["mot-cle"] !== "") {
            $motcle = (
                (stripos($offre["LibPoste"], $params["mot-cle"]) !== false) or
                (stripos($offre["Description"], $params["mot-cle"]) !== false) or
                (stripos($offre["Ville"], $params["mot-cle"]) !== false) or
                (stripos($offre["horaire"], $params["mot-cle"]) !== false) or
                (stripos($offre["LibSOC"], $params["mot-cle"]) !== false) or
                (stripos($offre["formation"], $params["mot-cle"]) !== false) or
                (stripos($offre["langues"], $params["mot-cle"]) !== false) or
                (stripos($offre["salaire"], $params["mot-cle"]) !== false));
        }
        // Selon commune
        if (
            $params["epine"] || $params["noirmoutier"] || $params["gueriniere"] || $params["barbatre"]
        ) {
            $commune = false;
            if ($params["epine"] && stripos($offre["Ville"], "L EPINE") !== false) {
                $commune = true;
            } elseif ($params["noirmoutier"] && stripos($offre["Ville"], "NOIRMOUTIER EN L ILE") !== false) {
                $commune = true;
            } elseif ($params["gueriniere"] && stripos($offre["Ville"], "LA GUERINIERE") !== false) {
                $commune = true;
            } elseif ($params["barbatre"] && stripos($offre["Ville"], "BARBATRE") !== false) {
                $commune = true;
            }
        }
        if ($params["hebergement"]) {
            $hebergement = ($offre["logementfourni"] == "1");
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
 * Calcule l'age en jours de l'offre passée en parametre ET AJOUTE CETTE VALEUR à l'offre
 * @param mixed $offre offre qui sera moifiée
 * @return int l'age de l'offre en jours
 */
function calculerAgeJours($offre)
{
    $date = DateTime::createFromFormat('Ymd', $offre['DateOffreAct']);
    $now = new DateTime();
    $ecart = $now->diff($date);
    return $ecart->days;
}

/**
 * Retounre l'age de l'offre sous forme de text
 * @param $ageJours l'age de l'annonce en jours
 * @return string text à afficher
 */
function afficherAgeJours($ageJours)
{
    if ($ageJours == 0) {
        $diff_string = "aujourd'hui";
    } elseif ($ageJours == 1) {
        $diff_string = "hier";
    } else {
        $diff_string = "actualisée il y a " . $ageJours . " jours";
    }
    return $diff_string;
}

/**
 * Genère un string contenant des elements <option> pour chaque profession unique trouvé, dans l'ordre alphabetique.
 * @param mixed $toutes_offres les offes avant filtrage
 * @return string code html
 */
function creerHtmlProfessionsUniques($toutes_offres)
{
    // Prendre toutes les professions (même les duplicates)
    foreach ($toutes_offres as $offre) {
        $professions[] = $offre["LibPoste"];
    }

    // Prendre que les professions uniques
    $professions = array_unique($professions);

    // Trier par ordre alphabetique
    sort($professions);

    // Creer code html
    $string_options_html = "";
    foreach ($professions as $profession) {
        $string_options_html .= "<option value=\"$profession\">$profession</option>";
    }
    return $string_options_html;
}

/**
 * Charge le code html du <select> professions en cache recente ou le reproduit à partir du JSON et le stocke en cache
 * @return string string de html à afficher
 */
function getProfessionsUniques()
{
    global $ageMaxCache, $cacheProfessions;

    $professions_html = "";
    $utiliser_cache = false;

    // --- Essayer de charger à partir du cache ---
    if (file_exists($cacheProfessions) && is_readable($cacheProfessions)) {
        $cacheModifiedTime = filemtime($cacheProfessions);
        $currentTime = time();

        // Verifie si cache est recent
        if ($cacheModifiedTime !== false && ($currentTime - $cacheModifiedTime) < $ageMaxCache) {
            $cachedData = file_get_contents($cacheProfessions);
            if ($cachedData !== false) {
                $unserializedData = @unserialize($cachedData);

                // Verifie la déserialisation et que les donnees sont dans la bonne format
                if ($unserializedData !== false && is_string($unserializedData)) {
                    $professions_html = $unserializedData;
                    $utiliser_cache = true;
                }
            }
        }
    }

    // --- Autrement chargement et préparation à partir du json et stockage en cache ---
    if (!$utiliser_cache) {
        // Obtention de toutes les offres d'emploi du JSON
        global $dbaccess;
        $toutes_offres_non_traitees = $dbaccess->chargerToutesOffresJSON();
        $professions_html = creerHtmlProfessionsUniques($toutes_offres_non_traitees);

        // After successfully loading from DB and processing, save to cache
        if (!empty($professions_html)) {
            $serializedData = serialize($professions_html);
            file_put_contents($cacheProfessions, $serializedData);
        }
    }
    return $professions_html;
}

/**
 * Genère un string contenant des elements <option> pour chaque evenement unique trouvé, dans l'ordre alphabetique.
 * @param mixed $toutes_offres les offes avant filtrage
 * @return string code html
 */
function creerHtmlEvenements(array $toutes_offres)
{
    // Prendre toutes les evenements (même les duplicates)
    foreach ($toutes_offres as $offre) {
        $evenements[] = $offre["EvenementOffre"];
    }

    // Prendre que les evenements uniques et non vides
    $evenements = array_unique($evenements);
    $evenements = array_filter($evenements, function ($value) {
        return trim($value) !== "";
    });

    // Trier par ordre alphabetique
    sort($evenements);

    // Creer code html
    $string_options_html = "";
    foreach ($evenements as $evenement) {
        $string_options_html .= "<option value=\"$evenement\">$evenement</option>";
    }
    return $string_options_html;
}

/**
 * Charge le code html du <select> evenements en cache recente ou le reproduit à partir du JSON et le stocke en cache
 * @return string string de html à afficher
 */
function getEvenements()
{
    global $ageMaxCache, $cacheEvenements;

    $evenements_html = "";
    $utiliser_cache = false;

    // --- Essayer de charger à partir du cache ---
    if (file_exists($cacheEvenements) && is_readable($cacheEvenements)) {
        $cacheModifiedTime = filemtime($cacheEvenements);
        $currentTime = time();

        // Verifie si cache est recent
        if ($cacheModifiedTime !== false && ($currentTime - $cacheModifiedTime) < $ageMaxCache) {
            $cachedData = file_get_contents($cacheEvenements);
            if ($cachedData !== false) {
                $unserializedData = @unserialize($cachedData);

                // Verifie la déserialisation et que les donnees sont dans la bonne format
                if ($unserializedData !== false && is_string($unserializedData)) {
                    $evenements_html = $unserializedData;
                    $utiliser_cache = true;
                }
            }
        }
    }

    // --- Autrement chargement et préparation à partir du json et stockage en cache ---
    if (!$utiliser_cache) {
        // Obtention de toutes les offres d'emploi du JSON
        global $dbaccess;
        $toutes_offres_non_traitees = $dbaccess->chargerToutesOffresJSON();
        $evenements_html = creerHtmlEvenements($toutes_offres_non_traitees);

        // After successfully loading from DB and processing, save to cache
        if (!empty($evenements_html)) {
            $serializedData = serialize($evenements_html);
            file_put_contents($cacheEvenements, $serializedData);
        }
    }
    return $evenements_html;
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
 * Valide le GET parametre NumOffre
 * @return string NumOffre validé
 */
function validerParamNumOffre()
{
    return htmlspecialchars(trim($_GET['NUMOFFRE']));
}

/**
 * Ajoute pour chaque offre une clé avec la representation html pour afficher dans la liste d'offres
 * @param mixed $offres
 * @return void
 */
function creerHtmlListe(&$offres)
{
    foreach ($offres as &$offre) {
        $diff_string = afficherAgeJours($offre['ageJours']);
        $html_string = "
                <div class=\"job-list-item\">
                    <div class=\"job-list-item-left\">
                        <div class=\"job-list-row\">
                            <h3>{$offre['LibPoste']}</h3>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>label</span>
                            <p>Référence de l'offre : {$offre['NumOffre']} ($diff_string)</p>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>contract</span>
                            <p>{$offre['TypeContrat']}</p>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>distance</span>
                            <p>{$offre['Ville']}</p>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>account_box</span>
                            <p>{$offre['Contact']}</p>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>clarify</span>
                            <p>" . substr($offre['Description'], 0, 300) . "...</p>
                        </div>
                    </div>
                    <div class=\"job-list-item-right\">
                        <button onclick=\"location.href = 'offre.php?NUMOFFRE={$offre['NumOffre']}'\">Voir</button>
                    </div>
                </div>
                ";
        $offre['htmlListe'] = $html_string;
    }
    unset($offre);
}

/**
 * Rechercher offre specific dans la base de données
 * @param string $num_offre numéro identifiant de l'offre
 * @param array $toutes_offres array contenant l'ensemble d'offres
 * @return mixed array de l'offre s'il exist, null autrement
 */
function getOffreParNum(string $num_offre, array $toutes_offres)
{
    $offres_filtrees = array_filter($toutes_offres, fn(array $offre) => $offre["NumOffre"] === $num_offre);
    if (!empty($offres_filtrees)) {
        $offre = reset($offres_filtrees);
        return $offre;
    }
    return null;
}

/**
 * Charge les offres, triées et traitées, dans le cache recent. Autrement charge et traite les offres du JSON, et les met en cache
 * @return array toutes les offres triées et traitées
 */
function getToutesOffres()
{
    global $ageMaxCache, $cacheOffres;

    $toutes_offres = [];
    $utiliser_cache = false;

    // --- Essayer de charger à partir du cache ---
    if (file_exists($cacheOffres) && is_readable($cacheOffres)) {
        $cacheModifiedTime = filemtime($cacheOffres);
        $currentTime = time();

        // Verifie si cache est recent
        if ($cacheModifiedTime !== false && ($currentTime - $cacheModifiedTime) < $ageMaxCache) {
            $cachedData = file_get_contents($cacheOffres);
            if ($cachedData !== false) {
                $unserializedData = @unserialize($cachedData);

                // Verifie la déserialisation et que les donnees sont dans la bonne format
                if ($unserializedData !== false && is_array($unserializedData)) {
                    $toutes_offres = $unserializedData;
                    $utiliser_cache = true;
                }
            }
        }
    }

    // --- Autrement chargement et préparation à partir du json et stockage en cache ---
    if (!$utiliser_cache) {
        // Obtention de toutes les offres d'emploi du JSON
        global $dbaccess;
        $toutes_offres_non_traitees = $dbaccess->chargerToutesOffresJSON();
        $toutes_offres = traiterOffresJSON($toutes_offres_non_traitees);

        // After successfully loading from DB and processing, save to cache
        if (!empty($toutes_offres)) {
            $serializedData = serialize($toutes_offres);
            file_put_contents($cacheOffres, $serializedData);
        }
    }
    return $toutes_offres;
}

/**
 * Réalise diverses opérations de traitement sur les offres, comme le triage et la creation de code html.
 * @param mixed $toutes_offres_non_traitees
 * @return array les offres après traitement
 */
function traiterOffresJSON($toutes_offres_non_traitees)
{
    // Calculer l'age des offres
    foreach ($toutes_offres_non_traitees as &$offre) {
        $offre['ageJours'] = calculerAgeJours($offre);
    }
    unset($offre);

    // Trier les offres du plus récent au plus ancien (ageJours croissant)
    usort($toutes_offres_non_traitees, function ($a, $b) {
        return $a['ageJours'] <=> $b['ageJours'];
    });

    // Creer representation html pour la liste d'offres
    creerHtmlListe($toutes_offres_non_traitees);

    // Formatter description pour page offres
    foreach ($toutes_offres_non_traitees as &$offre) {
        $offre['formatDescription'] = formatterDescriptif($offre['Description']);
    }
    $toutes_offres_traitees = $toutes_offres_non_traitees;
    return $toutes_offres_traitees;
}

/**
 * Charger d'abord les vues des offres, incrementer celui passé en parametre (ou creer le compteur si manquant), puis enregistre dans le même fichier json. Retourne le nombre de vues pour l'ensemble d'offres
 * @param mixed $num_offre
 */
function incrementerVues($num_offre)
{
    global $dbaccess;
    $data_compteur_offres = $dbaccess->chargerVues();
    $trouve = false;
    foreach ($data_compteur_offres as $key => $value) {
        if ($key == $num_offre) {
            $data_compteur_offres[$key]++;
            $trouve = true;
            break;
        }
    }

    // Creation de clé et valeur si non trouvé
    if (!$trouve) {
        $data_compteur_offres[$num_offre] = 1;
    }

    // Enregistrement dans json
    $dbaccess->enregistrerVues($data_compteur_offres);

    return $data_compteur_offres;
}
