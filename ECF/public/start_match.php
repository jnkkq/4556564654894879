<?php
// Vérifiez si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté et est un commentateur
$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

include '../includes/db.php';

// Récupérer l'ID du match et le score final depuis le formulaire
$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;
$final_score = isset($_POST['final_score']) ? $_POST['final_score'] : '';

if ($match_id <= 0 || empty($final_score)) {
    echo "Données invalides.";
    exit();
}

// Requête SQL pour mettre à jour le score et l'heure de fin du match
$query_update = 'UPDATE matches SET final_score = :final_score, end_time = NOW(), status = "closed" WHERE id = :match_id';
$stmt_update = $pdo->prepare($query_update);
$stmt_update->execute([
    'final_score' => $final_score,
    'match_id' => $match_id
]);

// Calcul des gains/pertes des utilisateurs
$query_bets = 'SELECT b.user_id, b.amount, b.team_id, u.username
               FROM bets b
               JOIN users u ON b.user_id = u.id
               WHERE b.match_id = :match_id';
$stmt_bets = $pdo->prepare($query_bets);
$stmt_bets->execute(['match_id' => $match_id]);
$bets = $stmt_bets->fetchAll(PDO::FETCH_ASSOC);

foreach ($bets as $bet) {
 
    $winning_team = determine_winning_team($final_score); // Fonction à créer pour déterminer l'équipe gagnante

    if ($bet['team_id'] == $winning_team) {
        $winnings = $bet['amount'] * get_odds($match_id, $bet['team_id']); // Fonction à créer pour obtenir les cotes
     
    } else {
        $winnings = 0;
    }

   
}

 
header('Location: commentator.php');
exit();

 
function determine_winning_team($score) {
   
}

function get_odds($match_id, $team_id) {
}
?>
