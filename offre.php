<?php
include_once "includes/config.php"; // Paramêtres PHP comme affichage d'erreurs
include_once "includes/functions.php"; // Functions utilitaires communes
include_once "includes/dbaccess.php"; // Class d'accès aux données
$dbaccess = new Dbaccess(); // Creation d'objet d'accès aux données

// Recherche de l'offre selon son numéro
$offre = $dbaccess->getOffreParNum(validerParamNumOffre());

?>

<?php include "includes/header.php" ?>

<!DOCTYPE html lang="fr">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $offre["PROFESSION"] ?></title>
    <meta name="description" content="Consulter les offres d'emploi de l'île de Noirmoutier">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="screen.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="icon" href="https://www.cdc-iledenoirmoutier.com/themes/custom/noirmoutiercc/favicon.png"
        type="image/png">
</head>

<body>
    <main>
        <div class="row">
            <div class="cta" data-paragraph-animate-component="cta"
                style="translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);">
                <a href=""><span class="cta-label" data-label="Revenir aux offres">Revenir aux
                        offres</span></a>
            </div>
            <div class="cta" data-paragraph-animate-component="cta"
                style="translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);">
                <a onclick="window.print()"><span class="cta-label" data-label="Imprimer offre">Imprimer
                        offre</span></a>
            </div>
        </div>
        <section class="job-offer-section">
            <h2 class="title--2"><?= $offre["PROFESSION"] ?></h2>
            <p class="center">
                <small><?= "Référence de l'offre : " . $offre["NUMOFFRE"] . " (" . afficherEcartTemps($offre) . ")" ?></small>
            </p>
        </section>
        <section class="job-offer-section">
            <h3 class="title--4">Critères de l'offre</h3>
            <?php
            if (estRempli($offre["LIEU"])) {
                echo "<p><strong>Lieux : </strong>{$offre['LIEU']}</p>";
            }
            if (estRempli($offre["CONTRAT"])) {
                echo "<p><strong>Type de contrat : </strong>{$offre['CONTRAT']}</p>";
            }
            if (estRempli($offre["HORAIRES"])) {
                echo "<p><strong>Horaires : </strong>{$offre['HORAIRES']}</p>";
            }
            if (estRempli($offre["EXPERIENCE"])) {
                echo "<p><strong>Expérience : </strong>{$offre['EXPERIENCE']}</p>";
            }
            if (estRempli($offre["DEPLACEMENT"])) {
                echo "<p><strong>Déplacement : </strong>{$offre['DEPLACEMENT']}</p>";
            }
            if (estRempli($offre["SALAIRE"])) {
                echo "<p><strong>Salaire : </strong>{$offre['SALAIRE']}</p>";
            }
            if (estRempli($offre["FORMATION"])) {
                echo "<p><strong>Formation : </strong>{$offre['FORMATION']}</p>";
            }
            ?>
        </section>
        <section class="job-offer-section">
            <h3 class="title--4">Description du poste</h3>
            <?= formatterDescriptif($offre["DESCRIPTIF"]) ?>
        </section>
        <section class="job-offer-section">
            <h3 class="title--4">Contact</h3>
            <?php
            if (estRempli($offre["CONTACT"])) {
                echo "<p>{$offre['CONTACT']}</p>";
            }
            ?>
        </section>
        <div class="row">
            <div class="cta" data-paragraph-animate-component="cta"
                style="translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);">
                <a href=""><span class="cta-label" data-label="Revenir aux offres">Revenir aux
                        offres</span></a>
            </div>
            <div class="cta" data-paragraph-animate-component="cta"
                style="translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);">
                <a href="" onclick="window.print()"><span class="cta-label" data-label="Imprimer offre">Imprimer
                        offre</span></a>
            </div>
        </div>
    </main>

    <script src="main.js" async defer></script>
</body>

</html>

<?php include "includes/footer.php" ?>