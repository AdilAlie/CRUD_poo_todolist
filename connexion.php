<?php

// 1. ALLUMAGE DE LA MÉMOIRE (Obligatoire tout en haut)
session_start();


$env = parse_ini_file(__DIR__ . '/.env');

$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$username = $env['DB_USER'];
$password = $env['DB_PASS'];

// On crée la variable $pdo ici. Elle contient la connexion à MariaDB.
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?> 