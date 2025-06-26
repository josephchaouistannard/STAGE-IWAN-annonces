<?php

$host = 'localhost';
$dbname = 'testpdo';
$user = 'postgres';
$pass = 'admin';

$connexion = new PDO('pgsql:host='.$host.';dbname='.$dbname, $user, $pass);

$num_offre = 13917;

$requete = $connexion->prepare("SELECT * FROM offres where num_offre = ?");
$requete->execute([$num_offre]);
foreach ($requete as $row) {
  print_r($row);
}