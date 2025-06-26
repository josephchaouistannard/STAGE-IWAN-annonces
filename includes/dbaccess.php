<?php
/**
 * Class d'access aux données (actuellement pour fichier JSON)
 * 
 * Contient des méthodes pour obtenir toutes les offres, et une offre spécifique par son numéro
 */
class Dbaccess
{
    /**
     * Retourne toutes les offres contenus dans un fichier JSON.
     * @param bool $returnArray Vrai retourne un array associatif, Faux retourne des objets (non utilisé).
     * @return mixed array ou objet si reussi, null autrement
     */
    function getToutesOffres(bool $returnArray = true)
    {
        $file_contents = file_get_contents(dirname(__DIR__) . "/db.json");
        $data = json_decode($file_contents, $returnArray);
        if ($data) {
            return $data;
        }
        return null;
    }

    /**
     * Rechercher offre specific dans la base de données
     * @param string $num_offre numéro identifiant de l'offre
     * @return mixed array de l'offre s'il exist, null autrement
     */
    function getOffreParNum(string $num_offre)
    {
        $offres_filtrees = array_filter($this->getToutesOffres(), fn(array $offre) => $offre["NumOffre"] === $num_offre);
        if (!empty($offres_filtrees)) {
            $offre = reset($offres_filtrees);
            return $offre;
        }
        return null;
    }
}
