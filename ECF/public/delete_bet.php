<?php
include '../includes/db.php';
include '../includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$bet_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($bet_id === 0) {
    echo "Identifiant de pari invalide.";
    exit();
}

// Vérifiez si le pari appartient à l'utilisateur
$query_check = 'SELECT * FROM bets WHERE id = :bet_id AND user_id = :user_id';
$stmt_check = $pdo->prepare($query_check);
$stmt_check->execute(['bet_id' => $bet_id, 'user_id' => $user_id]);
$bet = $stmt_check->fetch(PDO::FETCH_ASSOC);

if (!$bet) {
    echo "Le pari n'existe pas ou n'appartient pas à l'utilisateur.";
    exit();
}

// Supprimer le pari
$query_delete = 'DELETE FROM bets WHERE id = :bet_id';
$stmt_delete = $pdo->prepare($query_delete);
$stmt_delete->execute(['bet_id' => $bet_id]);

header('Location: bet_history.php'); // Redirige vers la page de l'historique des paris
exit();
?>
