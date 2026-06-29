<?php

$host = '127.0.0.1';
$dbname = 'projet_crud';
$username = 'adil';
$password = 'adiladil';

// On crée la variable $pdo ici. Elle contient la connexion à MariaDB.
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>