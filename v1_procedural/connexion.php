<?php
// 1. Les variables de configuration
// TRÈS IMPORTANT : Même si depuis ton Mac tu utilisais 192.168...
// Ici, on met 127.0.0.1 car ce code PHP s'exécute DIRECTEMENT à l'intérieur du serveur !
$host = '127.0.0.1';
$dbname = 'projet_crud'; // Le nom de la base de données qu'on a créée
$username = 'adil'; // L'utilisateur dédié qu'on a créé (pas de root !)
$password = 'adiladil'; // Remplacer par le VRAI mot de passe que tu as choisi

try {
    // 2. On tente de construire le pont PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // 3. On configure PDO pour qu'il nous affiche les vraies erreurs en cas de problème (crucial pour le débug)
    // PDO est configuré pour échouer en silence, ce qui rend le débogage impossible.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Petit message de test qu'on effacera plus tard
    echo "Succès : Le pont PDO vers MariaDB est parfaitement fonctionnel !";

    // Si l'alarme sonne, PHP saute immédiatement tout le reste du bloc try et atterrit dans le catch.
} catch (PDOException $e) {
    // 4. Si le pont s'effondre (mauvais mot de passe, etc.), on arrête tout (die) et on affiche l'erreur
    die("Erreur fatale de connexion : " . $e->getMessage());
}
?>