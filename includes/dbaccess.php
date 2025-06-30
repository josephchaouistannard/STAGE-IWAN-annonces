<?php
/**
 * Class d'access aux données (actuellement pour fichier JSON)
 *
 * Contient une méthode pour obtenir toutes les offres, non triées et sans préparation de présentation html etc
 * Et des methodes pour lire et enregistrer le fichier json du compteur de vues
 */
class Dbaccess
{
    /**
     * Retourne toutes les offres contenus dans un fichier JSON.
     * @return mixed array si reussi, array vide autrement
     */
    function chargerToutesOffresJSON()
    {
        $chemin_min = dirname(__DIR__) . "/db.json";
        $chemin_maj = dirname(__DIR__) . "/db.JSON";

        $contenu_fichier = file_get_contents($chemin_min);

        // Si .json ne marche pas, essaie .JSON
        if ($contenu_fichier === false || $contenu_fichier === '') {
            $contenu_fichier = file_get_contents($chemin_maj);
        }

        $data = json_decode($contenu_fichier, true);

        if ($data) {
            return $data;
        }
        return [];
    }

    /**
     * Charge le vues par offre dans compteur.json, retourne un array associatif
     */
    function chargerVues() {
        $chemin_min = dirname(__DIR__) . "/compteur.json";
        $chemin_maj = dirname(__DIR__) . "/compteur.JSON";

        $contenu_fichier = file_get_contents($chemin_min);

        // Si .json ne marche pas, essaie .JSON
        if ($contenu_fichier === false || $contenu_fichier === '') {
             $contenu_fichier = file_get_contents($chemin_maj);
        }

        $data = json_decode($contenu_fichier, true);

        if ($data) {
            return $data;
        }
        return [];
    }

    /**
     * Enregistre les données compteur vues passé en parametre dans fichier json
     * @param mixed $data_compteur_offres
     * @return void
     */
    function enregistrerVues($data_compteur_offres) {
        $json_data = json_encode($data_compteur_offres, JSON_PRETTY_PRINT);
        // Toujours enregistrer compteur.json
        file_put_contents(dirname(__DIR__) . "/compteur.json", $json_data);
    }
}