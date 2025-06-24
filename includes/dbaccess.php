<?php
class Dbaccess
{
    function getAllJobData($returnArray = true)
    {
        $file_contents = file_get_contents(dirname(__DIR__) . "/db.json");
        $data = json_decode($file_contents, $returnArray);
        if ($data) {
            return $data;
        }
        return null;
    }

    function displayCount(array $filtered_data)
    {
        $count = count($filtered_data);
        if ($count > 1) {
            return "$count offres trouvées";
        }
        if ($count === 1) {
            return "1 offre trouvée";
        }
        return "pas d'offre trouvée";
    }

    function getOffreNum($num_offre, array $unfiltered_data)
    {
        $filtered = array_filter($unfiltered_data["offres"], function (array $offre) use ($num_offre) {
            return $offre["NUMOFFRE"] === $num_offre;
        });
        return reset($filtered);
    }
}

function getDiffString(array $offre)
{
    $date = DateTime::createFromFormat('d/m/Y', $offre['DATE_ACTU']);
    $now = new DateTime();
    $diff = $now->diff($date);
    if ($diff->days === 0) {
        $diff_string = "aujourd'hui";
    } elseif ($diff->days === 1) {
        $diff_string = "hier";
    } else {
        $diff_string = "il y a " . $diff->days . " jours";
    }
    return $diff_string;
}

function getProfessionsUniques(array $all_data)
{
    $professions = [];
    foreach ($all_data["offres"] as $offre) {
        $professions[] = $offre["PROFESSION"];
    }
    $professions = array_unique($professions);
    $string = "";
    foreach ($professions as $profession) {
        $string .= "<option value=\"$profession\">$profession</option>";
    }
    return $string;
}
