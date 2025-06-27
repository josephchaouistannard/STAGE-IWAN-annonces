<?php
/**
 * Class d'access aux données (actuellement pour fichier JSON)
 * 
 * Contient une méthode pour obtenir toutes les offres, non triées et sans préparation de présentation html etc
 */
class Dbaccess
{
    /**
     * Retourne toutes les offres contenus dans un fichier JSON.
     * @return mixed array si reussi, array vide autrement
     */
    function chargerToutesOffresJSON()
    {
        $file_contents = file_get_contents(dirname(__DIR__) . "/db.json");
        $data = json_decode($file_contents, true);
        if ($data) {
            return $data;
        }
        return [];
    }
}
