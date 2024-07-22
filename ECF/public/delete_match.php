<?php
// Inclusion du fichier de connexion à la base de données
include '../includes/db.php';

// Démarrage de la session pour accéder aux informations de l'utilisateur
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérification du rôle de l'utilisateur
$user_id = $_SESSION['user_id'];
$query_role = 'SELECT role FROM users WHERE id = :user_id';
$stmt_role = $pdo->prepare($query_role);
$stmt_role->execute(['user_id' => $user_id]);
$user = $stmt_role->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['role'] !== 'admin') {
    echo "Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.";
    exit();
}

// Récupération de l'identifiant du match à supprimer
$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($match_id > 0) {
    try {
        // Commence une transaction
        $pdo->beginTransaction();

        // Supprimer les paris associés au match
        $delete_bets_query = 'DELETE FROM bets WHERE match_id = :match_id';
        $stmt_delete_bets = $pdo->prepare($delete_bets_query);
        $stmt_delete_bets->execute(['match_id' => $match_id]);

        // Supprimer le match
        $delete_match_query = 'DELETE FROM matches WHERE id = :id';
        $stmt_delete_match = $pdo->prepare($delete_match_query);
        $stmt_delete_match->execute(['id' => $match_id]);

        // Commit la transaction
        $pdo->commit();

        // Redirection vers la page d'administration après suppression
        header('Location: admin_page.php');
        exit();
    } catch (Exception $e) {
        // Annule la transaction en cas d'erreur
        $pdo->rollBack();
        echo "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    echo "Identifiant de match invalide.";
    exit();
}
?>
