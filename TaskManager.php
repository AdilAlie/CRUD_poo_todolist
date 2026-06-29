<?php
class TaskManager {
    private $pdo;

    // L'allumage
    public function __construct($connexion_pdo) {
        $this->pdo = $connexion_pdo;
    }

    // Lecture globale
    public function getAllTaches() {
        $sql = "SELECT * FROM taches ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    // Lecture unique
    public function getTacheById($id) {
        $sql = "SELECT * FROM taches WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Création
    public function createTache($titre) {
        $sql = "INSERT INTO taches (titre) VALUES (:titre)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['titre' => $titre]);
    }

    // Modification
    public function updateTache($id, $titre) {
        $sql = "UPDATE taches SET titre = :titre WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['titre' => $titre, 'id' => $id]);
    }

    // Suppression
    public function deleteTache($id) {
        $sql = "DELETE FROM taches WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
    }
}
?>