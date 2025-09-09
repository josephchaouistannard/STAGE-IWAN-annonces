<?php
require_once "includes/functions.php"; // Functions utilitaires communes  (contient aussi class d'accès aux données et config.php)


$toutes_offres = getToutesOffres();
$professions_uniques = getProfessionsUniques();
$durees = getDureesUniques();
$evenements = getEvenements();
// Filtrage selon requête GET
$offres_filtrees = filtrerOffres($toutes_offres, validerParamsFiltrage());

// Verification de l'affichage de vues
if (isset($_GET['vues']) || isset($_GET['VUES'])) {
    $_SESSION['afficher_vues'] = true;
} else {
    if (!isset($_SESSION['afficher_vues'])) {
        $_SESSION['afficher_vues'] = false;
    }
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $index_page_titre ?></title>
    <meta name="description" content="<?= $index_et_offre_page_description ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat&family=Noto+Serif+Display:wght@500&family=Roboto:wght@400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1004); ?>">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="icon" href="assets/favicon.png" type="image/png">
</head>

<body>
    <?php include "includes/header.php" ?>
    <main>
        <form id="filters-form" method="get">
            <div class="form-row" id="form-header">
                <h3>FILTREZ !</h3>
                <p id="job-count"><?= afficherCompteOffres($offres_filtrees); ?></p>
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
                    <?= $professions_uniques ?>
                </select>
            </div>
            <div class="form-row"><label for="duree">Durée</label>
                <select class="filters-form-select" name="duree" id="duree">
                    <?= $durees ?>
                </select>
            </div>
            <div class="form-row"><label for="evenement">Evénement</label>
                <select class="filters-form-select" name="evenement" id="evenement">
                    <option value="tous" selected>Tous événements confondus</option>
                    <?= $evenements ?>
                </select>
            </div>
            <div class="form-row"><label for="mot-cle">Mot clé</label>
                <input type="text" name="mot-cle" id="mot-cle" placeholder="Cuisine...">
            </div>
            <div class="form-row">
                <div id="container-villes">
                    <h5>Filtrez par commune :</h5>
                    <?php echo creerHtmlGroupesGeographique() ?>
                </div>
            </div>
            <div class="form-row" id="hebergement-container">
                <input type="checkbox" id="hebergement" name="hebergement" value="1">
                <label for="hebergement">Uniquement avec hébergement</label>
            </div>
            <div class="form-row">
                <button id="submit-filters">Rechercher</button>
                <button onclick="reinitialiserFiltersForm(event)">Réinitialiser</button>
            </div>
        </form>

        <div id="job-list">
            <?php foreach ($offres_filtrees as $offre) {
                echo $offre['htmlListe'];
            } ?>
        </div>
        <div id="pagination-controls">
        </div>
    </main>
    <?php include "includes/footer.php" ?>
    <script src="main.js"></script>
</body>

</html>