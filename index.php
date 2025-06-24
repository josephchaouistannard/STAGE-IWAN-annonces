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
            $contrat = false;
            if (stripos($offre["CONTRAT"], $_GET["contrat"]) !== false) {
                $contrat = true;
            }
        }
        if (isset($_GET["profession"]) and $_GET["profession"] !== "" and $_GET["profession"] !== "tous") {
            $profession = $offre["PROFESSION"] === $_GET["profession"];
        }
        if ($_GET["evenement"] !== "" and $_GET["evenement"] !== "tous") {
        }
        if ($_GET["mot-cle"] !== "") {
        }
        if (
            isset($_GET["epine"]) || isset($_GET["noirmoutier"]) || isset($_GET["gueriniere"]) || isset($_GET["barbatre"])
        ) {
        }
        if (isset($_GET["hebergement"])) {
        }
        return $contrat and $profession and $evenement and $motcle and $commune and $hebergement;
    });
    return $filtered_data;
}

$data = $dbaccess->getAllJobData();
$filtered_data = applyFilters($dbaccess->getAllJobData(true));

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
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
</head>

<body>
    <main>
        <form id="filters-form">
            <div class="form-row" id="form-header">
                <h3>FILTREZ !</h3>
                <p id="job-count"><?= $dbaccess->displayCount($filtered_data); ?></p>
            </div>
            <div class="form-row"><label for="contrat">Contrat</label>
                <select name="contrat" id="contrat">
                    <option value="tous" selected>Tous</option>
                    <option value="cdd">CDD</option>
                    <option value="cdi">CDI</option>
                    <option value="cesu">CESU</option>
                    <option value="extra">EXTRA</option>
                    <option value="saisonnier">CONTRAT SAISONNIER</option>
                </select>
            </div>
            <div class="form-row"><label for="profession">Profession</label>
                <select name="profession" id="profession">
                    <option value="tous" selected>Toutes</option>
                    <?= getProfessionsUniques($data); ?>
                </select>
            </div>
            <div class="form-row"><label for="duree">Durée</label>
                <select name="duree" id="duree">
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
                <select name="evenement" id="evenement">
                    <option value="tous" selected>Tous événements confondus</option>
                </select>
            </div>
            <div class="form-row"><label for="mot-cle">Mot clé</label>
                <input type="text" name="mot-cle" id="mot-cle" placeholder="restauration...">
            </div>
            <div class="form-row">
                <div id="container-villes">
                    <h5>Filtrez par commune :</h5>
                    <input type="checkbox" id="noirmoutier" name="noirmoutier" value="1">
                    <label for="noirmoutier">Noirmoutier-en-l'île</label>
                    <br>
                    <input type="checkbox" id="epine" name="epine" value="1">
                    <label for="epine">L'Epine</label>
                    <br>
                    <input type="checkbox" id="gueriniere" name="gueriniere" value="1">
                    <label for="gueriniere">La Guérinière</label>
                    <br>
                    <input type="checkbox" id="barbatre" name="barbatre" value="1">
                    <label for="barbatre">Barbâtre</label>
                </div>
            </div>
            <div class="form-row" id="hebergement-container">
                <input type="checkbox" id="hebergement" name="hebergement" value="1">
                <label for="hebergement">Uniquement avec hébergement</label>
            </div>
            <div class="form-row">
                <input type="submit" value="Rechercher">
                <button onclick="resetForm()">Réinitialiser</button>
            </div>
        </form>

        <div id="job-list">
            <?php
            foreach ($filtered_data as $offre) {
                $diff_string = getDiffStringArray($offre);
                echo "
                <div class=\"job-list-item\">
                    <div class=\"job-list-item-left\">
                        <div class=\"job-list-row\">
                            <h3>{$offre['PROFESSION']}</h3>
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
                        <button onclick=\"window.location.href='offre.php?NUMOFFRE={$offre['NUMOFFRE']}';\">Voir</button>
                    </div>
                </div>
                ";
            }

            ?>

        </div>
    </main>

    <script src="main.js"></script>
</body>

</html>

<?php include "includes/footer.php" ?>