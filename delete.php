<?php
// 1. On importe le pont vers MariaDB
require_once 'connexion.php';

// 2. On vérifie si un paramètre "id" a bien été envoyé dans l'URL
if (isset($_GET['id'])) {
    
    // On capture ce fameux ID (le code-barres de la tâche)
    $id_a_supprimer = $_GET['id'];

    // 3. On prépare l'ordre d'exécution (toujours en 2 étapes pour la sécurité)
    // TRÈS IMPORTANT : Ne JAMAIS oublier le "WHERE". Si tu l'oublies, MariaDB vide TOUTE ta table.
    $sql = "DELETE FROM taches WHERE id = :ide";
    $stmt = $pdo->prepare($sql);
    
    // 4. On exécute en donnant le code-barres exact
    $stmt->execute(['ide' => $id_a_supprimer]);
}

// 5. Mission accomplie, on téléporte l'utilisateur vers la page d'accueil
header("Location: index.php");
exit;
?>