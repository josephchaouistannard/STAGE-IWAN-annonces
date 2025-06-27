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

    /**
     * Charge le vues par offre dans compteur.json, retourne un array associatif
     */
    function chargerVues() {
        $file_contents = file_get_contents(dirname(__DIR__) . "/compteur.json");
        $data = json_decode($file_contents, true);
        if ($data) {
            return $data;
        }
        return [];
    }

    /**
     * Charger d'abord les vues des offres, incrementer celui passé en parametre (ou creer le compteur si manquant), puis enregistre dans le même fichier json. Retourne le nombre de vues pour l'ensemble d'offres
     * @param mixed $num_offre
     */
    function incrementerVues($num_offre) {
        $data_compteur_offres = $this->chargerVues();
        $trouve = false;
        foreach ($data_compteur_offres as $key => $value) {
            if ($key == $num_offre) {
                $data_compteur_offres[$key]++;
                $trouve = true;
                break;
            }
        }

        // Creation de clé et valeur si non trouvé
        if (!$trouve) {
            $data_compteur_offres[$num_offre] = 1;
        }

        // Encode the updated data back to JSON
        $json_data = json_encode($data_compteur_offres, JSON_PRETTY_PRINT);
        // Save the JSON data back to the file
        file_put_contents(dirname(__DIR__) . "/compteur.json", $json_data);

        return $data_compteur_offres;
    }
}
