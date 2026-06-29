<?php
require_once 'connexion.php';
require_once 'TaskManager.php';

if (isset($_GET['id'])) {
    // 1. On allume le robot
    $gestionnaire = new TaskManager($pdo);
    
    // 2. On capture l'ID de l'URL
    $id_a_supprimer = $_GET['id'];

    // 3. On ordonne au robot de détruire cette tâche
    $gestionnaire->deleteTache($id_a_supprimer);
}

header("Location: index.php");
exit;
?>