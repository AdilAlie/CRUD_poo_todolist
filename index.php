<?php
require_once 'connexion.php';
require_once 'TaskManager.php';

// ===================================================
// LE CONTENEUR DE SÉCURITÉ (Try/Catch)
// ===================================================
try {
    $gestionnaire = new TaskManager($pdo);

    // CSRF : Création du Jeton
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];

    // ===================================================
    // PARTIE 1 : TRAITEMENTS DES ACTIONS (POST et GET)
    // ===================================================

    // Action A : Ajouter une tâche (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("Erreur de sécurité : Jeton CSRF invalide.");
        }

        $texte_saisi = trim($_POST['titre']);
        if (strlen($texte_saisi) === 0 || strlen($texte_saisi) > 255) {
            throw new Exception("Erreur de validation : Le titre doit faire entre 1 et 255 caractères.");
        }

        $gestionnaire->createTache($texte_saisi);
        header("Location: index.php");
        exit;
    }

    // Action B : Modifier une tâche (POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("Erreur de sécurité : Jeton CSRF invalide.");
        }

        $id_cible = $_POST['id'];
        
        $nouveau_texte = trim($_POST['titre']);
        if (strlen($nouveau_texte) === 0 || strlen($nouveau_texte) > 255) {
            throw new Exception("Erreur de validation : Le titre doit faire entre 1 et 255 caractères.");
        }

        $gestionnaire->updateTache($id_cible, $nouveau_texte);
        header("Location: index.php");
        exit;
    }

    // Action C : Supprimer une tâche (GET)
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        if (!isset($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
            throw new Exception("Erreur de sécurité : Tentative de suppression non autorisée.");
        }

        $id_a_supprimer = $_GET['id'];
        $gestionnaire->deleteTache($id_a_supprimer);
        header("Location: index.php");
        exit;
    }

    // ===================================================
    // PARTIE 2 : PRÉPARATION DES DONNÉES
    // ===================================================
    $taches = $gestionnaire->getAllTaches();
    $tache_a_modifier = null;
    
    if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $tache_a_modifier = $gestionnaire->getTacheById($_GET['id']);
    }

} catch (Throwable $e) {
    error_log("[" . date('Y-m-d H:i:s') . "] Erreur : " . $e->getMessage() . "\n", 3, __DIR__ . '/erreurs.log');
    die("Une erreur système est survenue. L'opération a été annulée. Veuillez réessayer plus tard.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        /* ===================================================
           VARIABLES CSS (Configuration centralisée)
           =================================================== */
        :root {
            --couleur-fond: #f4f4f9;
            --couleur-texte: #333;
            --couleur-bordure: #2c3e50;
            --couleur-primaire: #3498db;
            --couleur-edit: #36d188ff;
            --couleur-remove: #e74c3c;
            --couleur-succes: #2ecc71;
            --rayon-bordure: 8px;
        }

        /* ===================================================
           REMISE À ZÉRO ET TYPOGRAPHIE
           =================================================== */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--couleur-texte);
            display: flex;
            justify-content: center;
            padding: 40px 20px;

            /* ===================================================
            LE FOND "QUADRILLAGE CAHIER" (Pure CSS)
            =================================================== */
            background-color: #f1ececff; /* Couleur du papier (blanc cassé) */
            background-image: 
                /* Lignes horizontales (bleu très clair) */
                linear-gradient(rgba(52, 152, 219, 0.2) 1px, transparent 1px),
                /* Lignes verticales (bleu très clair, tournées à 90 degrés) */
                linear-gradient(90deg, rgba(52, 152, 219, 0.2) 1px, transparent 1px);
            
            /* La taille d'un carreau (20 pixels sur 20 pixels) */
            background-size: 20px 20px; 
        }

        /* ===================================================
           LE GRAND CADRE EXTÉRIEUR (Ta maquette)
           =================================================== */
        .app-container {
            width: 100%;
            max-width: 600px;
            background: #ffffff;
            border: 2px solid var(--couleur-bordure);
            border-radius: var(--rayon-bordure);
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* ===================================================
           LE FORMULAIRE EN HAUT
           =================================================== */
        .form-tache {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .form-tache__input {
            flex: 1; /* L'input prend toute la place restante */
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: var(--rayon-bordure);
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-tache__input:focus {
            border-color: var(--couleur-primaire);
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: var(--rayon-bordure);
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            color: white;
            transition: opacity 0.3s;
            font-size: 16px;
        }

        .btn:hover {
            opacity: 0.8;
        }

        .btn--ajouter { background-color: var(--couleur-primaire); }
        .btn--modifier { background-color: var(--couleur-succes); }
        
        .lien-annuler {
            display: flex;
            align-items: center;
            color: var(--couleur-texte);
            text-decoration: none;
            font-size: 14px;
        }

        .lien-annuler:hover {
            text-decoration: underline;
        }

        /* ===================================================
           LA LISTE ET LES TÂCHES (Le cœur de ton dessin)
           =================================================== */
        .liste-taches {
            display: flex;
            flex-direction: column;
            gap: 15px; /* Espacement entre les tâches */
        }

        .tache {
            display: flex;
            justify-content: space-between; /* Titre à gauche, boutons à droite */
            align-items: center; /* Centrage vertical */
            border: 2px solid var(--couleur-bordure);
            border-radius: var(--rayon-bordure);
            padding: 15px 20px;
            background-color: #fafafa;
        }

        .tache__titre {
            font-size: 18px;
            font-weight: 500;
            word-break: break-word; /* Empêche le texte de dépasser si trop long */
        }

        .tache__actions {
            display: flex;
            gap: 14px; /* Espace entre Edit et Remove */
        }

        .tache__btn {
            font-size: 14px;
            text-decoration: none;
            font-weight: bold;
        }

        .tache__btn--edit { color: var(--couleur-edit); }
        .tache__btn--remove { color: var(--couleur-remove); }

        .tache__btn:hover { text-decoration: underline; }

    </style>
</head>
<body>

    <main class="app-container">

        <?php if ($tache_a_modifier): ?>
            <form action="index.php" method="POST" class="form-tache">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="id" value="<?php echo $tache_a_modifier['id']; ?>">
                
                <input type="text" name="titre" class="form-tache__input" value="<?php echo htmlspecialchars($tache_a_modifier['titre']); ?>" required>
                <button type="submit" class="btn btn--modifier">Mettre à jour</button>
                <a href="index.php" class="lien-annuler">Annuler</a>
            </form>
        <?php else: ?>
            <form action="index.php" method="POST" class="form-tache">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <input type="text" name="titre" class="form-tache__input" placeholder="Nouvelle tâche..." required>
                <button type="submit" class="btn btn--ajouter">Ajouter</button>
            </form>
        <?php endif; ?>

        <div class="liste-taches">
            <?php foreach ($taches as $tache): ?>
                
                <article class="tache">
                    <span class="tache__titre">
                        <?php echo htmlspecialchars($tache['titre']); ?>
                    </span>
                    
                    <div class="tache__actions">
                        <a href="index.php?action=edit&id=<?php echo $tache['id']; ?>" class="tache__btn tache__btn--edit">Edit</a>
                        <a href="index.php?action=delete&id=<?php echo $tache['id']; ?>&csrf_token=<?php echo $csrf_token; ?>" class="tache__btn tache__btn--remove">Remove</a>
                    </div>
                </article>

            <?php endforeach; ?>
        </div>

    </main>

</body>
</html>