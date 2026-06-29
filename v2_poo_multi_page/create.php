<?php
require_once 'connexion.php';
require_once 'TaskManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. On allume le robot
    $gestionnaire = new TaskManager($pdo);
    
    // 2. On capture ce que l'utilisateur a tapé
    $texte_saisi = $_POST['titre'];

    // 3. On ordonne au robot de créer la tâche avec ce texte
    $gestionnaire->createTache($texte_saisi);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter</title>
</head>
<body>
    <h2>Nouvelle Tâche</h2>
    <form action="create.php" method="POST">
        <div>
            <label for="titre">Nom de la tâche :</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        <button type="submit">Ajouter</button>
    </form>
    <a href="index.php">Retour</a>
</body>
</html>