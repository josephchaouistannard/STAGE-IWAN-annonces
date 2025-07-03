<?php
require_once "includes/functions.php"; // Functions utilitaires communes  (contient aussi class d'accès aux données et config.php)


$toutes_offres = getToutesOffres();
$professions_uniques = getProfessionsUniques($toutes_offres);
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

<?php include "includes/header.php" ?>

<!DOCTYPE html lang="fr">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Consultation des Offres</title>
    <meta name="description" content="Consulter les offres d'emploi de l'île de Noirmoutier">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat&family=Noto+Serif+Display:wght@500&family=Roboto:wght@400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1003); ?>">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="icon" href="assets/favicon.png" type="image/png">
</head>

<body>
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
                    <option value="tous" selected>Toutes durées</option>
                    <option value="1 jour">1 jour</option>
                    <option value="5 semaines">5 semaines</option>
                    <option value="1 mois">1 mois</option>
                    <option value="1.5 mois">1.5 mois</option>
                    <option value="2 mois">2 mois</option>
                    <option value="2.5 mois">2.5 mois</option>
                    <option value="3 mois">3 mois</option>
                    <option value="3.5 mois">3.5 mois</option>
                    <option value="4 mois">4 mois</option>
                    <option value="4.5 mois">4.5 mois</option>
                    <option value="5 mois">5 mois</option>
                    <option value="5.5 mois">5.5 mois</option>
                    <option value="6 mois">6 mois</option>
                    <option value="6.5 mois">6.5 mois</option>
                    <option value="7 mois">7 mois</option>
                    <option value="8 mois">8 mois</option>
                    <option value="A l'année">A l'année</option>
                    <option value="CDI">CDI</option>
                </select>
            </div>
            <div class="form-row"><label for="evenement">Evénement</label>
                <select class="filters-form-select" name="evenement" id="evenement">
                    <option value="tous" selected>Tous événements confondus</option>
                </select>
            </div>
            <div class="form-row"><label for="mot-cle">Mot clé</label>
                <input type="text" name="mot-cle" id="mot-cle" placeholder="Cuisine...">
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
    <script src="main.js"></script>
</body>

</html>

<?php include "includes/footer.php" ?>