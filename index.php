<?php


?>

<?php include "includes/header.php" ?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Consultation des Offres</title>
    <meta name="description" content="Consulter les offres d'emploi de l'île de Noirmoutier">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <main>
        <form id="filters-form">
            <div class="form-row" id="form-header">
                <h3>FILTREZ !</h3>
                <p id="job-count">100 temp</p>
            </div>
            <div class="form-row"><label for="contrat">Contrat</label>
                <select name="contrat" id="contrat" value="Tous" required>
                    <option value="tous" selected>Tous</option>
                </select>
            </div>
            <div class="form-row"><label for="profession">Profession</label>
                <select name="profession" id="profession" value="Toutes" required>
                    <option value="tous" selected>Toutes</option>
                </select>
            </div>
            <div class="form-row"><label for="duree">Durée</label>
                <select name="duree" id="duree" value="Toutes durées" required>
                    <option value="tous" selected>Toutes durées</option>
                </select>
            </div>
            <div class="form-row"><label for="evenement">Evénement</label>
                <select name="evenement" id="evenement" value="Tous événements confondus" required>
                    <option value="tous" selected>Tous événements confondus</option>
                </select>
            </div>
            <div class="form-row"><label for="mot-cle">Mot clé</label>
                <input type="text" name="mot-cle" id="mot-cle" placeholder="restauration...">
            </div>
            <div class="form-row">
                <div id="container-villes">
                    <h5>Filtrez par commune :</h5>
                    <input type="checkbox" id="noirmoutier" name="noirmoutier" value="noirmoutier">
                    <label for="noirmoutier">Noirmoutier-en-l'île</label>
                    <br>
                    <input type="checkbox" id="epine" name="epine" value="epine">
                    <label for="epine">L'Epine</label>
                    <br>
                    <input type="checkbox" id="gueriniere" name="gueriniere" value="gueriniere">
                    <label for="gueriniere">La Guérinière</label>
                    <br>
                    <input type="checkbox" id="barbatre" name="barbatre" value="barbatre">
                    <label for="barbatre">Barbâtre</label>
                </div>
            </div>
            <div class="form-row" id="hebergement-container">
                <input type="checkbox" id="hebergement" name="hebergement" value="hebergement">
                <label for="hebergement">Uniquement avec hébergement</label>
            </div>
            <div class="form-row">
                <input type="submit" value="Rechercher">
            </div>
        </form>

        <div id="job-list">

        </div>
    </main>

    <script src="main.js" async defer></script>
</body>

</html>








<?php include "includes/footer.php" ?>