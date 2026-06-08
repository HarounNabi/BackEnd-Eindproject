<?php
$host = 'localhost';
$databasenaam = 'eindproject';
$gebruiker = 'bit_academy';
$wachtwoord = 'bit_academy';

$pdo = new PDO("mysql:host=$host;dbname=$databasenaam", $gebruiker, $wachtwoord);
