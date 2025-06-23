<?php
class Dbaccess
{
    function getAllJobData($returnArray = false)
    {
        $file_contents = file_get_contents(dirname(__DIR__) . "/db.json");
        $data = json_decode($file_contents);
        if ($data) {
            return $data;
        }
        return null;
    }

    function displayCount()
    {
        global $data;
        $count = count((array) $data->offres);
        if ($count > 1) {
            return "$count offres trouvées";
        }
        if ($count === 1) {
            return "1 offre trouvée";
        }
        return "pas d'offre trouvée";
    }

    function getOffreNum($num_offre, $unfiltered_data)
    {
        $filtered = array_filter((array) $unfiltered_data->offres, function ($offre) use ($num_offre) {
            return $offre->NUMOFFRE === $num_offre;
        });
        return reset($filtered);
    }
}

function getDiffString($offre)
{
    $date = DateTime::createFromFormat('d/m/Y', $offre->DATE_ACTU);
    $now = new DateTime();
    $diff = $now->diff($date);
    if ($diff === 0) {
        $diff_string = "aujourd'hui";
    } elseif ($diff === 1) {
        $diff_string = "hier";
    } else {
        $diff_string = "il y a " . $diff->d . " jours";
    }
    return $diff_string;
}

function getDiffStringArray($offre)
{
    $date = DateTime::createFromFormat('d/m/Y', $offre['DATE_ACTU']);
    $now = new DateTime();
    $diff = $now->diff($date);
    if ($diff->days === 0) {
        return "aujourd'hui";
    } elseif ($diff->days === 1) {
        return "hier";
    } else {
        return "il y a " . $diff->days . " jours";
    }
}

function getProfessionsUniques($all_data)
{
    $professions = [];
    foreach ($all_data->offres as $offre) {
        $professions[] = $offre->PROFESSION;
    }
    $professions = array_unique($professions);
    $string = "";
    foreach ($professions as $profession) {
        $string .= "<option value=\"$profession\">$profession</option>";
    }
    return $string;
}
