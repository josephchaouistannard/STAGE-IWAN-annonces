<?php

$driver = 'pgsql';
$host = 'localhost';
$port = '5432';
$dbname = 'testpdo';
$user = 'postgres';
$pass = 'admin';

class Dbaccess
{
    private $connexion = new PDO('pgsql:host='.$host.';port='.$port.';dbname='.$dbname, $user, $pass);

    function requeteSelect() {

    }
}



$num_offre = 13917;

$requete = $connexion->prepare("SELECT * FROM offres where num_offre = ?");
$requete->execute([$num_offre]);
foreach ($requete as $row) {
  print_r($row);
}