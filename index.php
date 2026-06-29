<?php
// On importe le moteur de connexion
// Si tu as une boucle complexe et que par erreur tu fais require 10 fois,
// Apache va ouvrir 10 ponts simultanés vers MariaDB. Le serveur va saturer et crasher.
// Le _once est une sécurité
require_once 'connexion.php';

// On va chercher les données dans MariaDB tout de suite, avant même de dessiner la page
$sql = "SELECT * FROM taches ORDER BY created_at DESC";
$stmt = $pdo->query($sql); // statement(reponse brute illisible)
$taches = $stmt->fetchAll(PDO::FETCH_ASSOC); //Fetch (Va chercher) All (Absolument tout), transforme les données brutes en un beau tableau PHP
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>To-Do List CRUD</title>
</head>
<body>

    <div>
        <a href="create.php">NEW +</a>
    </div>

    <hr>
    <?php foreach ($taches as $tache): ?>
        
        <div>
            <span><?php echo htmlspecialchars($tache['titre']); ?></span>
            
            <a href="update.php?id=<?php echo $tache['id']; ?>">Edit</a>
            <a href="delete.php?id=<?php echo $tache['id']; ?>">Remove</a>
        </div>

    <?php endforeach; ?>

</body>
</html>
