<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "includes/dbaccess.php";
$dbaccess = new Dbaccess();

function applyFilters(array $all_data)
{
    // duree=1&

    $filtered_data = array_filter($all_data["offres"], function ($offre) {
        $contrat = true;
        $profession = true;
        $evenement = true;
        $motcle = true;
        $commune = true;
        $hebergement = true;
        if (isset($_GET["contrat"]) and $_GET["contrat"] !== "" and $_GET["contrat"] !== "tous") {
            $contrat = (stripos($offre["CONTRAT"], $_GET["contrat"]) !== false);
        }
        if (isset($_GET["profession"]) and $_GET["profession"] !== "" and $_GET["profession"] !== "tous") {
            $profession = ($offre["PROFESSION"] === $_GET["profession"]);
        }
        if (isset($_GET["evenement"]) and $_GET["evenement"] !== "" and $_GET["evenement"] !== "tous") {
            // Add event filtering logic here if needed
        }
        if (isset($_GET["mot-cle"]) and $_GET["mot-cle"] !== "") {
            $motcle = (stripos($offre["PROFESSION"], $_GET["mot-cle"]) !== false);
            // Add search in other fields if needed
        }
        if (
            isset($_GET["epine"]) || isset($_GET["noirmoutier"]) || isset($_GET["gueriniere"]) || isset($_GET["barbatre"])
        ) {
            $commune = false;
            if (isset($_GET["epine"]) && $offre["LIEU"] === "L'Epine") {
                $commune = true;
            } elseif (isset($_GET["noirmoutier"]) && $offre["LIEU"] === "Noirmoutier-en-l'île") {
                $commune = true;
            } elseif (isset($_GET["gueriniere"]) && $offre["LIEU"] === "La Guérinière") {
                $commune = true;
            } elseif (isset($_GET["barbatre"]) && $offre["LIEU"] === "Barbâtre") {
                $commune = true;
            }
        }
        if (isset($_GET["hebergement"])) {
            $hebergement = ($offre["HEBERGEMENT"] === "oui");
        }
        return $contrat && $profession && $evenement && $motcle && $commune && $hebergement;
    });
    return $filtered_data;
}

$data = $dbaccess->getAllJobData();
$filtered_data = applyFilters($dbaccess->getAllJobData());
?>

<?php include "includes/header.php" ?>

<!DOCTYPE html lang="fr">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Consultation des Offres</title>
    <meta name="description" content="Consulter les offres d'emploi de l'île de Noirmoutier">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="screen.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="icon" href="https://www.cdc-iledenoirmoutier.com/themes/custom/noirmoutiercc/favicon.png" type="image/png">
</head>

<body>
    <div id="Page" class="page">
        <main>
            <form id="filters-form" method="get">
                <div class="form-row" id="form-header">
                    <h3 class="title--4">FILTREZ !</h3>
                    <p class="body--m--bold" id="job-count"><?= $dbaccess->displayCount($filtered_data); ?></p>
                </div>
                <div class="form-row"><label for="contrat">Contrat</label>
                    <select class="filters-form-select" name="contrat" id="contrat">
                        <option value="tous" selected>Tous</option>
                        <option value="cdd">CDD</option>
                        <option value="cdi">CDI</option>
                        <option value="cesu">CESU</option>
                        <option value="extra">EXTRA</option>
                        <option value="saisonnier">CONTRAT SAISONNIER</option>
                    </select>
                </div>
                <div class="form-row"><label for="profession">Profession</label>
                    <select class="filters-form-select" name="profession" id="profession">
                        <option value="tous" selected>Toutes</option>
                        <?= getProfessionsUniques($data); ?>
                    </select>
                </div>
                <div class="form-row"><label for="duree">Durée</label>
                    <select class="filters-form-select" name="duree" id="duree">
                        <option value="tous" selected>Toutes durées</option>
                        <option value="1">1 jour</option>
                        <option value="2">5 semaines</option>
                        <option value="3">1 mois</option>
                        <option value="4">1.5 mois</option>
                        <option value="5">2 mois</option>
                        <option value="6">2.5 mois</option>
                        <option value="7">3 mois</option>
                        <option value="8">3.5 mois</option>
                        <option value="9">4 mois</option>
                        <option value="10">4.5 mois</option>
                        <option value="11">5 mois</option>
                        <option value="12">5.5 mois</option>
                        <option value="13">6 mois</option>
                        <option value="14">6.5 mois</option>
                        <option value="15">7 mois</option>
                        <option value="16">8 mois</option>
                        <option value="17">A l'année</option>
                        <option value="18">CDI</option>
                    </select>
                </div>
                <div class="form-row"><label for="evenement">Evénement</label>
                    <select class="filters-form-select" name="evenement" id="evenement">
                        <option value="tous" selected>Tous événements confondus</option>
                    </select>
                </div>
                <div class="form-row"><label for="mot-cle">Mot clé</label>
                    <input type="text" name="mot-cle" id="mot-cle" placeholder="restauration...">
                </div>
                <div class="form-row">
                    <div id="container-villes">
                        <h5>Filtrez par commune :</h5>
                        <input type="checkbox" id="noirmoutier" name="noirmoutier" value="Noirmoutier-en-l'île">
                        <label for="noirmoutier">Noirmoutier-en-l'île</label>
                        <br>
                        <input type="checkbox" id="epine" name="epine" value="L'Epine">
                        <label for="epine">L'Epine</label>
                        <br>
                        <input type="checkbox" id="gueriniere" name="gueriniere" value="La Guérinière">
                        <label for="gueriniere">La Guérinière</label>
                        <br>
                        <input type="checkbox" id="barbatre" name="barbatre" value="Barbâtre">
                        <label for="barbatre">Barbâtre</label>
                    </div>
                </div>
                <div class="form-row" id="hebergement-container">
                    <input type="checkbox" id="hebergement" name="hebergement" value="oui">
                    <label for="hebergement">Uniquement avec hébergement</label>
                </div>
                <div class="form-row">
                    <div class="cta" data-paragraph-animate-component="cta"
                        style="translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);">
                        <a id="submit-filters"><span class="cta-label" data-label="Rechercher">Rechercher</span></a>
                    </div>
                    <div class="cta" data-paragraph-animate-component="cta"
                        style="translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);">
                        <a onclick="resetForm()"><span class="cta-label"
                                data-label="Réinitialiser">Réinitialiser</span></a>
                    </div>
                </div>
            </form>

            <div id="job-list">
                <?php
                foreach ($filtered_data as $offre) {
                    $diff_string = getDiffString($offre);
                    echo "
                <div class=\"job-list-item\">
                    <div class=\"job-list-item-left\">
                        <div class=\"job-list-row\">
                            <h3 class=\"title--4--book bold\">{$offre['PROFESSION']}</h3>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>label</span>
                            <p>Référence de l'offre : {$offre['NUMOFFRE']} ($diff_string)</p>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>contract</span>
                            <p>{$offre['CONTRAT']}</p>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>distance</span>
                            <p>{$offre['LIEU']}</p>
                        </div>
                        <div class=\"job-list-row\">
                            <span class='material-symbols-outlined'>account_box</span>
                            <p>{$offre['CONTACT_ENT']}</p>
                        </div>
                    </div>
                    <div class=\"job-list-item-right\">
                        <div class=\"cta\" data-paragraph-animate-component=\"cta\"
                        style=\"translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);\">
                        <a href=\"offre.php?NUMOFFRE={$offre['NUMOFFRE']}\"><span class=\"cta-label\"
                                data-label=\"Voir\">Voir</span></a>
                    </div>
                    </div>
                </div>
                ";
                }

                ?>

            </div>
        </main>
    </div>


    <script src="main.js"></script>
</body>

</html>

<?php include "includes/footer.php" ?>