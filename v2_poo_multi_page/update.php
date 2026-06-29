<?php
require_once 'connexion.php';
require_once 'TaskManager.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id_cible = $_GET['id'];

// 1. On allume le robot
$gestionnaire = new TaskManager($pdo);

// TRAITEMENT (Si on a cliqué sur Mettre à jour)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_texte = $_POST['titre'];
    
    // On donne au robot l'ID à modifier ET le nouveau texte
    $gestionnaire->updateTache($id_cible, $nouveau_texte);
    
    header("Location: index.php");
    exit;
}

// LECTURE (Pour pré-remplir la case)
// On demande au robot de nous donner uniquement la tâche qui correspond à cet ID
$tache = $gestionnaire->getTacheById($id_cible);

if (!$tache) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier</title>
</head>
<body>
    <h2>Modifier la tâche</h2>
    <form action="update.php?id=<?php echo $tache['id']; ?>" method="POST">
        <div>
            <label for="titre">Nom :</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($tache['titre']); ?>" required>
        </div>
        <button type="submit">Mettre à jour</button>
    </form>
    <a href="index.php">Annuler</a>
</body>
</html>