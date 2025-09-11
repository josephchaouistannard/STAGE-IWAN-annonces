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
        $groupe_geo = true;
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
            if (isset($offre["EvenementOffre"])) {
                $evenement = (trim($offre["EvenementOffre"]) == $params["evenement"]);
            } else {
                $evenement = false;
            }
        }
        // Mot clé dans CERTAINS CHAMPS SEULEMENT
        if (isset($params["mot-cle"]) and $params["mot-cle"] !== "") {
            // ignorer apostrohpe dans recherche
            $motcle_propre = str_replace("'", "", $params["mot-cle"]);

            // pour chaque comparaison, ignorer les apostrphes dans les données
            $motcle = (
                (isset($offre["LibPoste"]) && stripos(str_replace("'", "", $offre["LibPoste"]), $motcle_propre) !== false) or
                (isset($offre["Description"]) && stripos(str_replace("'", "", $offre["Description"]), $motcle_propre) !== false) or
                (isset($offre["Ville"]) && stripos(str_replace("'", "", $offre["Ville"]), $motcle_propre) !== false) or
                (isset($offre["horaire"]) && stripos(str_replace("'", "", $offre["horaire"]), $motcle_propre) !== false) or
                (isset($offre["LibSOC"]) && stripos(str_replace("'", "", $offre["LibSOC"]), $motcle_propre) !== false) or
                (isset($offre["formation"]) && stripos(str_replace("'", "", $offre["formation"]), $motcle_propre) !== false) or
                (isset($offre["langues"]) && stripos(str_replace("'", "", $offre["langues"]), $motcle_propre) !== false) or
                (isset($offre["salaire"]) && stripos(str_replace("'", "", $offre["salaire"]), $motcle_propre) !== false) or
                (isset($offre["NumOffre"]) && stripos(str_replace("'", "", $offre["NumOffre"]), $motcle_propre) !== false)
            );
        }
        // Selon groupe geographique
        if (isset($params["geo"]) && !empty($params["geo"])) {
            $groupe_geo = false;
            
            foreach ($params["geo"] as $groupe => $valeur) {
                if ($valeur && isset($offre["GroupeGeographique"])) {
                    // URL decode the ville name (handles %20 -> space conversion)
                    $groupe = urldecode($groupe);

                    $groupes_offre = explode('#', $offre["GroupeGeographique"]);

                    foreach ($groupes_offre as $groupe_offre) {
                        $groupe_offre = trim($groupe_offre);

                        if (stripos($groupe_offre, $groupe) !== false) {
                            $groupe_geo = true;
                            break 2;
                        }
                    }
                }
            }
        }
        if ($params["hebergement"]) {
             // Check if the key exists before accessing it
            if (isset($offre["logementfourni"])) { // Added check here
                $hebergement = ($offre["logementfourni"] == "1");
            } else {
                $hebergement = false; // If key doesn't exist, it doesn't match the filter
            }
        }
        return $contrat && $profession && $evenement && $motcle && $groupe_geo && $hebergement && $duree;
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
 *  * Genère un string contenant des elements <option> pour chaque duree trouvée, dans l'ordre defini dans la fonction.
 * @param mixed $toutes_offres
 * @return string
 */
function creerHtmlDurees($toutes_offres)
{
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

    // Prendre toutes les durees (même les duplicates)
    foreach ($toutes_offres as $offre) {
        $durees_dans_offres[] = $offre["DureeContrat"];
    }

    // Prendre que les durees uniques
    $durees_dans_offres = array_unique($durees_dans_offres);

    // Creer code html
    $string_options_html = "<option value=\"tous\" selected>Toutes durées</option>";

    // Parcourir les durées dans l'ordre souhaité
    foreach ($durees_possibles as $duree) {
        // Ajouter l'option seulement si cette durée existe dans les offres
        if (in_array($duree, $durees_dans_offres)) {
            $string_options_html .= "<option value=\"$duree\">$duree</option>";
        }
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
 *  * Charge le code html du <select> durees en cache recente ou le reproduit à partir du JSON et le stocke en cache
 * @return string
 */
function getDureesUniques() {
    global $ageMaxCache, $cacheDurees;

    $durees_html = "";
    $utiliser_cache = false;

    // --- Essayer de charger à partir du cache ---
    if (file_exists($cacheDurees) && is_readable($cacheDurees)) {
        $cacheModifiedTime = filemtime($cacheDurees);
        $currentTime = time();

        // Verifie si cache est recent
        if ($cacheModifiedTime !== false && ($currentTime - $cacheModifiedTime) < $ageMaxCache) {
            $cachedData = file_get_contents($cacheDurees);
            if ($cachedData !== false) {
                $unserializedData = @unserialize($cachedData);

                // Verifie la déserialisation et que les donnees sont dans la bonne format
                if ($unserializedData !== false && is_string($unserializedData)) {
                    $durees_html = $unserializedData;
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
        $durees_html = creerHtmlDurees($toutes_offres_non_traitees);

        // After successfully loading from DB and processing, save to cache
        if (!empty($durees_html)) {
            $serializedData = serialize($durees_html);
            file_put_contents($cacheDurees, $serializedData);
        }
    }
    return $durees_html;
}

/**
 * Genère un string contenant des elements <option> pour chaque evenement unique trouvé, dans l'ordre alphabetique.
 * @param mixed $toutes_offres les offes avant filtrage
 * @return string code html
 */
function creerHtmlEvenements(array $toutes_offres)
{
    // Prendre toutes les evenements (même les duplicates)
    $evenements = [];
    foreach ($toutes_offres as $offre) {
        // Verifie que clé existe avant d'accèder
        if (isset($offre["EvenementOffre"])) {
            $evenements[] = $offre["EvenementOffre"];
        }
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
    $params['geo'] = $_GET['geo'] ?? [];
    $params['hebergement'] = isset($_GET['hebergement']);

    // Nettoyer les valeurs
    $params['contrat'] = (trim($params['contrat']));
    $params['profession'] = (trim($params['profession']));
    $params['duree'] = (trim($params['duree']));
    $params['evenement'] = (trim($params['evenement']));
    $params['mot-cle'] = (trim($params['mot-cle']));

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
                            <p>" . utf8_substr($offre['Description'], 0, 300) . "...</p>
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

/**
 * Fonction pour couper une chaîne sans l'extension mbstring qui n'était pas dispo sur le serveur
 * @param string $str
 * @param int $start
 * @param int $length
 * @return string
 */
function utf8_substr(string $str, int $start, int $length = null): string
{
    // Split into an array of code points
    $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

    $slice = array_slice($chars, $start, $length);
    return implode('', $slice);
}

/**
 * Genère un string contenant le html des cases à cocher pour chaque groupe géographique.
 * @param mixed $toutes_offres les offes avant filtrage
 * @return string code html
 */
function creerHtmlGroupesGeographique()
{
    global $groupes_geographiques;
    // Creer code html    
    $string_groupe_html = "";
    foreach ($groupes_geographiques as $bdd => $affichage) {
        $string_groupe_html .= "
        <input type=\"checkbox\" id=\"$bdd\" name=\"geo[$bdd]\" value=\"1\">
        <label for=\"$bdd\">$affichage</label>
        <br>
        ";
    }

    return $string_groupe_html;
}

/**
 * Prendre le code html pour les filtres de groupes geographiques du json directement
 * @return string
 */
function getGroupesGeographiquesUniques() {
    // Obtention de toutes les offres d'emploi du JSON
    global $dbaccess;
    $toutes_offres = $dbaccess->chargerToutesOffresJSON();
    
    $groupes = array();
    
    // Prendre toutes les professions (même les duplicates)
    foreach ($toutes_offres as $offre) {
        // Split the GroupeGeographique by # delimiter
        $groupes_individuels = explode('#', $offre["GroupeGeographique"]);
        
        // Add each individual group to our array
        foreach ($groupes_individuels as $groupe) {
            // Trim whitespace and only add non-empty groups
            $groupe = trim($groupe);
            if (!empty($groupe)) {
                $groupes[] = $groupe;
            }
        }
    }
    
    // Prendre que les groupes uniques
    $groupes = array_unique($groupes);
    
    // Creer code html
    $string = "\$groupes_geographiques = [<br>";
    foreach ($groupes as $groupe) {
        $string .= "\"$groupe\" => \"\",<br>";
    }
    $string .= "];";
    return $string;
}


/**
 * Genère un string contenant le html du combo pour les types de contrat.
 * @param mixed $toutes_offres les offes avant filtrage
 * @return string code html
 */
function creerHtmlTypesDeContrat($toutes_offres)
{
    // Prendre toutes les professions (même les duplicates)
    foreach ($toutes_offres as $offre) {
        $types[] = $offre["TypeContrat"];
    }

    // Prendre que les professions uniques
    $types = array_unique($types);

    // Trier par ordre alphabetique
    sort($types);

    // Creer code html    
    $string_types_html = "";
    foreach ($types as $type) {
        $string_types_html .= "
        <option value=\"$type\">$type</option>
        ";
    }

    return $string_types_html;
}

/**
 * Prendre le code html pour le combo de types de contrat, soit de la cache recente, soit du json directement
 * @return string
 */
function getTypesDeContratUniques() {
    global $ageMaxCache, $cacheTypesDeContrat;

    $types_html = "";
    $utiliser_cache = false;

    // --- Essayer de charger à partir du cache ---
    if (file_exists($cacheTypesDeContrat) && is_readable($cacheTypesDeContrat)) {
        $cacheModifiedTime = filemtime($cacheTypesDeContrat);
        $currentTime = time();

        // Verifie si cache est recent
        if ($cacheModifiedTime !== false && ($currentTime - $cacheModifiedTime) < $ageMaxCache) {
            $cachedData = file_get_contents($cacheTypesDeContrat);
            if ($cachedData !== false) {
                $unserializedData = @unserialize($cachedData);

                // Verifie la déserialisation et que les donnees sont dans la bonne format
                if ($unserializedData !== false && is_string($unserializedData)) {
                    $types_html = $unserializedData;
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
        $types_html = creerHtmlDurees($toutes_offres_non_traitees);

        // After successfully loading from DB and processing, save to cache
        if (!empty($types_html)) {
            $serializedData = serialize($types_html);
            file_put_contents($cacheTypesDeContrat, $serializedData);
        }
    }
    return $types_html;
}