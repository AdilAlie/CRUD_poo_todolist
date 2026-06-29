<?php
// On importe le pont vers la base de données
require_once 'connexion.php';

// On vérifie si l'utilisateur vient de cliquer sur le bouton "Ajouter" du formulaire (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // On capture le texte que l'utilisateur a tapé dans la case "titre"
    $tache_saisi = $_POST['titre'];

    // On prépare la commande SQL (toujours avec la sécurité des deux étapes)
    $sql = "INSERT INTO taches (titre) VALUES (:nom_de_la_nouvelle_tache)";
    $stmt = $pdo->prepare($sql);
    
    // On exécute en envoyant le texte capturé
    $stmt->execute(['nom_de_la_nouvelle_tache' => $tache_saisi]);
    // L'action est finie. On renvoie automatiquement l'utilisateur vers la page d'accueil.
    header("Location: index.php");
    exit; // On coupe le script ici pour être sûr que rien d'autre ne s'exécute
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une tâche</title>
</head>
<body>

    <h2>Nouvelle Tâche</h2>

    <form action="create.php" method="POST">
        
        <div>
            <label for="titre">Nom de la tâche :</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        
        <br>
        
        <button type="submit">Ajouter à la liste</button>
        
    </form>

    
    <hr>
    <a href="index.php">Retour</a>

</body>
</html>