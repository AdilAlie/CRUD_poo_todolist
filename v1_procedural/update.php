<?php
// On importe le pont
require_once 'connexion.php';

// 1. VÉRIFICATION DE L'URL (Le GET)
// Si quelqu'un essaie d'aller sur update.php sans donner d'ID, on le renvoie à l'accueil
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = $_GET['id'];

// 2. LE TRAITEMENT DE LA MODIFICATION (Le POST)
// Ce bloc s'active UNIQUEMENT quand on clique sur "Mettre à jour"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_titre = $_POST['titre'];

    // L'ordre SQL "UPDATE" modifie une ligne existante. Le "WHERE" est vital pour ne pas tout écraser.
    $sql_update = "UPDATE taches SET titre = :titre_maj WHERE id = :id_cible";
    $stmt_update = $pdo->prepare($sql_update);
    
    // On exécute avec les deux informations : le nouveau texte ET le code-barres de la tâche
    $stmt_update->execute([
        'titre_maj' => $nouveau_titre,
        'id_cible' => $id
    ]);

    header("Location: index.php");
    exit;
}

// 3. LA LECTURE AVANT MODIFICATION (Préparation de la page)
// Ce bloc s'exécute quand on arrive sur la page pour la première fois. On cherche l'ancien texte.
$sql_read = "SELECT * FROM taches WHERE id = :id_a_lire";
$stmt_read = $pdo->prepare($sql_read);
$stmt_read->execute(['id_a_lire' => $id]);

// fetch (sans "All") car on ne cherche qu'une seule ligne précise
$tache = $stmt_read->fetch(PDO::FETCH_ASSOC);

// Si la tâche n'existe plus dans MariaDB (déjà supprimée par exemple)
if (!$tache) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la tâche</title>
</head>
<body>

    <h2>Modifier la tâche</h2>

    <form action="update.php?id=<?php echo $tache['id']; ?>" method="POST">
        
        <div>
            <label for="titre">Nom de la tâche :</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($tache['titre']); ?>" required>
        </div>
        
        <br>
        
        <button type="submit">Mettre à jour</button>
        
    </form>

    <br>
    <hr>
    <a href="index.php">Annuler</a>

</body>
</html>