<?php
include_once "includes/dbaccess.php";
$dbaccess = new Dbaccess();

function formatDescription($text)
{
    if (empty(trim($text))) {
        return '';
    }

    $safe_text = htmlspecialchars($text);

    $formatted_text = nl2br($safe_text);

    $formatted_text = str_replace('VOS MISSIONS :', '<h4 class="title--4">VOS MISSIONS :</h4>', $formatted_text);
    $formatted_text = str_replace('PROFIL ET CONDITIONS DE TRAVAIL :', '<h4 class="title--4">PROFIL ET CONDITIONS DE TRAVAIL :</h4>', $formatted_text);
    $formatted_text = str_replace('CANDIDATURE :', '<h4 class="title--4">CANDIDATURE :</h4>', $formatted_text);

    $formatted_text = preg_replace('/(<br\s*\/?>\s*|^)>\s*/', '$1• ', $formatted_text);

    return trim($formatted_text);
}

function isValid($field)
{
    return !empty(trim($field));
}

$default_back_url = 'index.php';
$referer = $_SERVER['HTTP_REFERER'] ?? null;
$back_url = $default_back_url;

if ($referer && filter_var($referer, FILTER_VALIDATE_URL)) {
    $current_host = $_SERVER['HTTP_HOST'];
    $referer_host = parse_url($referer, PHP_URL_HOST);
    $current_scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
    $referer_scheme = parse_url($referer, PHP_URL_SCHEME);
    $current_port = $_SERVER['SERVER_PORT'] ?? ($current_scheme === 'https' ? 443 : 80);
    $referer_port = parse_url($referer, PHP_URL_PORT);
    if ($referer_port === null) {
        $referer_port = ($referer_scheme === 'https' ? 443 : 80);
    }
    if ($referer_scheme === $current_scheme && $referer_host === $current_host && $referer_port == $current_port) {
        $back_url = $referer;
    }
}

$num_offre = $_GET["NUMOFFRE"];
$data = $dbaccess->getAllJobData();
$offre = $dbaccess->getOffreNum($num_offre, $data);

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
                <a href="<?= $back_url ?>"><span class="cta-label" data-label="Revenir aux offres">Revenir aux
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
                <small><?= "Référence de l'offre : " . $num_offre . " (" . getDiffString($offre) . ")" ?></small></p>
        </section>
        <section class="job-offer-section">
            <h3 class="title--4">Critères de l'offre</h3>
            <?php
            if (isValid($offre["LIEU"])) {
                echo "<p><strong>Lieux : </strong>{$offre['LIEU']}</p>";
            }
            if (isValid($offre["CONTRAT"])) {
                echo "<p><strong>Type de contrat : </strong>{$offre['CONTRAT']}</p>";
            }
            if (isValid($offre["HORAIRES"])) {
                echo "<p><strong>Horaires : </strong>{$offre['HORAIRES']}</p>";
            }
            if (isValid($offre["EXPERIENCE"])) {
                echo "<p><strong>Expérience : </strong>{$offre['EXPERIENCE']}</p>";
            }
            if (isValid($offre["DEPLACEMENT"])) {
                echo "<p><strong>Déplacement : </strong>{$offre['DEPLACEMENT']}</p>";
            }
            if (isValid($offre["SALAIRE"])) {
                echo "<p><strong>Salaire : </strong>{$offre['SALAIRE']}</p>";
            }
            if (isValid($offre["FORMATION"])) {
                echo "<p><strong>Formation : </strong>{$offre['FORMATION']}</p>";
            }
            ?>
        </section>
        <section class="job-offer-section">
            <h3 class="title--4">Description du poste</h3>
            <?= formatDescription($offre["DESCRIPTIF"]) ?>
        </section>
        <section class="job-offer-section">
            <h3 class="title--4">Contact</h3>
            <?php
            if (isValid($offre["CONTACT"])) {
                echo "<p>{$offre['CONTACT']}</p>";
            }
            ?>
        </section>
        <div class="row">
            <div class="cta" data-paragraph-animate-component="cta"
                style="translate: none; rotate: none; scale: none; opacity: 1; transform: translate(0px);">
                <a href="<?= $back_url ?>"><span class="cta-label" data-label="Revenir aux offres">Revenir aux
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