<?php
require_once "includes/functions.php"; // Functions utilitaires communes (contient aussi class d'accès aux données et config.php)

$toutes_offres = getToutesOffres();
// Recherche de l'offre selon son numéro
$offre = getOffreParNum(validerParamNumOffre(), $toutes_offres);

// Charger et incrementer les vues
$data_compteur = incrementerVues($offre['NumOffre']);

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
    <title><?= $offre["LibPoste"] ?></title>
    <meta name="description" content="Consulter les offres d'emploi de l'île de Noirmoutier">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat&family=Noto+Serif+Display:wght@500&family=Roboto:wght@400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles.cssv=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . 'styles.css'); ?>">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <link rel="icon" href="assets/favicon.png"
        type="image/png">
</head>

<body>
    <main>
        <div class="row">
            <button class="js-back-button">Revenir aux offres</button>
            <button onclick="window.print()">Imprimer offre</button>
        </div>
        <section class="job-offer-section">
            <h2><?= $offre["LibPoste"] ?></h2>
            <p class="center">
                <small><?= "Référence de l'offre : " . $offre["NumOffre"] . " (" . afficherAgeJours(calculerAgeJours($offre)) . ")" ?>
                <?php if ($_SESSION['afficher_vues']) {
                    echo "<br>Vue " . $data_compteur[$offre['NumOffre']] . " fois";
                } ?></small>
            </p>
        </section>
        <section class="job-offer-section">
            <h3>Critères de l'offre</h3>
            <?php
            if (estRempli($offre["Ville"])) {
                echo "<p><strong>Lieux : </strong>{$offre['Ville']}</p>";
            }
            if (estRempli($offre["TypeContrat"])) {
                echo "<p><strong>Type de contrat : </strong>{$offre['TypeContrat']}</p>";
            }
            if (estRempli($offre["horaire"])) {
                echo "<p><strong>Horaires : </strong>{$offre['horaire']}</p>";
            }
            if (estRempli($offre["experience"])) {
                echo "<p><strong>Expérience : </strong>{$offre['experience']}</p>";
            }
            if (estRempli($offre["deplacement"])) {
                echo "<p><strong>Déplacement : </strong>{$offre['deplacement']}</p>";
            }
            if (estRempli($offre["salaire"])) {
                echo "<p><strong>Salaire : </strong>{$offre['salaire']}</p>";
            }
            if (estRempli($offre["formation"])) {
                echo "<p><strong>Formation : </strong>{$offre['formation']}</p>";
            }
            ?>
        </section>
        <section class="job-offer-section">
            <h3>Description du poste</h3>
            <?= $offre["formatDescription"] ?>
        </section>
        <section class="job-offer-section">
            <h3>Contact</h3>
            <?php
            if (estRempli($offre["Contact"])) {
                echo "<p>{$offre['Contact']}</p>";
                echo "<p>{$offre['email']}</p>";
            }
            ?>
        </section>
        <div class="row">
            <button class="js-back-button">Revenir aux offres</button>
            <button onclick="window.print()">Imprimer offre</button>
        </div>
    </main>

    <script src="main.js" async defer></script>

    <script>
        const backButtons = document.querySelectorAll('.js-back-button');
        backButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                if (document.referrer && document.referrer.includes(window.location.hostname)) {
                    history.back();
                } else {
                    window.location.href = '/index.php';
                }
            });
        });
    </script>


</body>

</html>

<?php include "includes/footer.php" ?>