<?php
include_once "includes/dbaccess.php";
$dbaccess = new Dbaccess();

function formatDescription($text)
{
    if (empty($text)) {
        return '';
    }

    // 1. Sanitize potential HTML/special characters in the original text *first*
    $safe_text = htmlspecialchars($text);

    // 2. Convert newline characters to HTML breaks
    $formatted_text = nl2br($safe_text);

    // 3. Format section headings (basic example: bold known headings)
    // You might need to adjust these based on the actual data patterns
    $formatted_text = str_replace('VOS MISSIONS :', '<h4>VOS MISSIONS :</h4>', $formatted_text);
    $formatted_text = str_replace('PROFIL ET CONDITIONS DE TRAVAIL :', '<h4>PROFIL ET CONDITIONS DE TRAVAIL :</h4>', $formatted_text);
    $formatted_text = str_replace('CANDIDATURE :', '<h4>CANDIDATURE :</h4>', $formatted_text);
    // Add more str_replace for other potential heading patterns if needed

    // 4. Format list items (replace "> " at start of line/after <br> with a bullet)
    // This regex looks for "> " immediately after a <br /> tag or at the very beginning of the string
    // and replaces it with the preceding <br /> (or nothing if at start) plus a bullet point and space.
    $formatted_text = preg_replace('/(<br\s*\/?>\s*|^)>\s*/', '$1• ', $formatted_text);

    // You could potentially add more formatting here if you notice other patterns,
    // like converting URLs into clickable links, etc.

    return trim($formatted_text);
}

function isValid($field)
{
    return !empty(trim($field));
}

$default_back_url = 'index.php';
$referer = $_SERVER['HTTP_REFERER'] ?? null;
$back_url = $default_back_url;

// Check if the referer exists and is a valid URL
if ($referer && filter_var($referer, FILTER_VALIDATE_URL)) {
    // Get the host of the current page
    $current_host = $_SERVER['HTTP_HOST'];
    // Get the host of the referer page
    $referer_host = parse_url($referer, PHP_URL_HOST);
    // Get the scheme (http or https) for a more robust origin check
    $current_scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
    $referer_scheme = parse_url($referer, PHP_URL_SCHEME);
    // Get the port for a complete origin check
    $current_port = $_SERVER['SERVER_PORT'] ?? ($current_scheme === 'https' ? 443 : 80);
    $referer_port = parse_url($referer, PHP_URL_PORT);
    if ($referer_port === null) {
        // Default port based on scheme if not specified in referer URL
        $referer_port = ($referer_scheme === 'https' ? 443 : 80);
    }
    // Check if the referer is from the same origin (same scheme, host, and port)
    if ($referer_scheme === $current_scheme && $referer_host === $current_host && $referer_port == $current_port) {
        // The referer is from the same site and origin. Use the referer URL.
        // This URL should contain the original GET parameters (filters).
        $back_url = $referer;
    }
    // If the referer is from a different site, $back_url remains the $default_back_url
    // If referer was invalid or empty, $back_url also remains the $default_back_url
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
    <title><?= $data->offres->PROFESSION ?></title>
    <meta name="description" content="Consulter les offres d'emploi de l'île de Noirmoutier">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
</head>

<body>
    <main>
        <div class="row">
            <button onclick="window.location.href='<?= $back_url ?>';">Revenir aux offres</button>
            <button onclick="window.print()">Imprimer offre</button>
        </div>
        <section>
            <h2><?= $offre->PROFESSION ?></h2>
            <p><small><?= "Référence de l'offre : " . $num_offre . " (" . getDiffString($offre) . ")" ?></small></p>
        </section>
        <section>
            <h3>Critères de l'offre</h3>
            <?php
            if (isValid($offre->LIEU)) {
                echo "<p><strong>Lieux : </strong>$offre->LIEU</p>";
            }
            if (isValid($offre->CONTRAT)) {
                echo "<p><strong>Type de contrat : </strong>$offre->CONTRAT</p>";
            }
            if (isValid($offre->HORAIRES)) {
                echo "<p><strong>Horaires : </strong>$offre->HORAIRES</p>";
            }
            if (isValid($offre->EXPERIENCE)) {
                echo "<p><strong>Expérience : </strong>$offre->EXPERIENCE</p>";
            }
            if (isValid($offre->DEPLACEMENT)) {
                echo "<p><strong>Déplacement : </strong>$offre->DEPLACEMENT</p>";
            }
            if (isValid($offre->SALAIRE)) {
                echo "<p><strong>Salaire : </strong>$offre->SALAIRE</p>";
            }
            if (isValid($offre->FORMATION)) {
                echo "<p><strong>Formation : </strong>$offre->FORMATION</p>";
            }
            ?>
        </section>
        <section>
            <h3>Description du poste</h3>
            <?= formatDescription($offre->DESCRIPTIF) ?>
        </section>
        <section>
            <h3>Contact</h3>
            <?php
            if (isValid($offre->CONTACT)) {
                echo "<p>$offre->CONTACT</p>";
            }
            ?>
        </section>
        <div class="row">
            <button onclick="window.location.href='<?= $back_url ?>';">Revenir aux offres</button>
            <button onclick="">Imprimer offre</button>
        </div>
    </main>

    <script src="main.js" async defer></script>
</body>

</html>








<?php include "includes/footer.php" ?>