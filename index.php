<?php
require_once 'connexion.php';
require_once 'TaskManager.php';

// 1. On allume le robot en lui injectant $pdo
$gestionnaire = new TaskManager($pdo);

// 2. On demande au robot de nous donner les tâches, et on les stocke dans $taches
$taches = $gestionnaire->getAllTaches();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>To-Do List POO</title>
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