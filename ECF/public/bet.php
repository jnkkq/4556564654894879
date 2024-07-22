<?php
include '../includes/db.php';
include '../includes/header.php';

// Vérifiez si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifiez le rôle de l'utilisateur
$user_id = $_SESSION['user_id'];
$query_role = 'SELECT role FROM users WHERE id = :user_id';
$stmt_role = $pdo->prepare($query_role);
$stmt_role->execute(['user_id' => $user_id]);
$user = $stmt_role->fetch(PDO::FETCH_ASSOC);

// Vérifiez si l'utilisateur a un rôle autorisé
$allowed_roles = ['admin', 'user', 'commentator'];
if (!$user || !in_array($user['role'], $allowed_roles)) {
    echo "Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.";
    exit();
}

$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($match_id === 0) {
    echo "ID de match invalide.";
    exit();
}

// Requête SQL pour récupérer les détails du match et les cotes
$query = 'SELECT m.*, t1.name AS team1_name, t2.name AS team2_name, o.odds_team1, o.odds_team2
          FROM matches m
          JOIN teams t1 ON m.team1 = t1.id
          JOIN teams t2 ON m.team2 = t2.id
          LEFT JOIN odds o ON m.id = o.match_id
          WHERE m.id = :match_id';
$stmt = $pdo->prepare($query);
$stmt->execute(['match_id' => $match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    echo "Match non trouvé. ID de match: " . htmlspecialchars($match_id);
    exit();
}

// Vérifiez si une mise a déjà été placée pour ce match
$query_bet = 'SELECT * FROM bets WHERE user_id = :user_id AND match_id = :match_id';
$stmt_bet = $pdo->prepare($query_bet);
$stmt_bet->execute([
    'user_id' => $_SESSION['user_id'],
    'match_id' => $match_id
]);
$existing_bet = $stmt_bet->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire de mise
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = (int)$_POST['team_id'];
    $amount = (float)$_POST['amount'];

    // Vérifiez si l'équipe existe dans la base de données
    $team_check_query = 'SELECT id FROM teams WHERE id = :team_id';
    $team_check_stmt = $pdo->prepare($team_check_query);
    $team_check_stmt->execute(['team_id' => $team_id]);
    $team_exists = $team_check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$team_exists) {
        echo "ID d'équipe invalide.";
        exit();
    }

    if ($existing_bet) {
        if ($amount == 0) {
            // Si le montant est 0, supprimez la mise
            $query = 'DELETE FROM bets WHERE user_id = :user_id AND match_id = :match_id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'match_id' => $match_id
            ]);
            echo "Votre mise a été supprimée.";
        } else {
            // Sinon, mettez à jour la mise existante
            $query = 'UPDATE bets SET team_id = :team_id, amount = :amount WHERE user_id = :user_id AND match_id = :match_id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'match_id' => $match_id,
                'team_id' => $team_id,
                'amount' => $amount
            ]);
            echo "Votre mise a été mise à jour avec succès!";
        }
    } else {
        if ($amount > 0) {
            // Si aucune mise n'existe et que le montant est supérieur à 0, insérez une nouvelle mise
            $query = 'INSERT INTO bets (user_id, match_id, team_id, amount) VALUES (:user_id, :match_id, :team_id, :amount)';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'match_id' => $match_id,
                'team_id' => $team_id,
                'amount' => $amount
            ]);
            echo "Votre mise a été placée avec succès!";
        } else {
            echo "Le montant doit être supérieur à 0.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parier sur le Match</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/main.js" defer></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main class="content">
        <section class="match-details">
            <h1>Pariez sur le match</h1>
            <div class="match-info">
                <div class="team-info">
                    <h2><?= htmlspecialchars($match['team1_name']) ?> vs <?= htmlspecialchars($match['team2_name']) ?></h2>
                    <p><strong>Date :</strong> <?= htmlspecialchars($match['date']) ?></p>
                    <p><strong>Heure :</strong> <?= htmlspecialchars($match['start_time']) ?> à <?= htmlspecialchars($match['end_time']) ?></p>
                    <p><strong>Météo :</strong> <?= htmlspecialchars($match['weather']) ?></p>
                </div>
                <div class="odds-info">
                    <h3>Cotes des équipes</h3>
                    <p><strong><?= htmlspecialchars($match['team1_name']) ?> :</strong> <?= htmlspecialchars($match['odds_team1']) ?></p>
                    <p><strong><?= htmlspecialchars($match['team2_name']) ?> :</strong> <?= htmlspecialchars($match['odds_team2']) ?></p>
                </div>
            </div>

            <section class="bet-form">
                <h3>Placer une mise</h3>
                <form action="bet.php?id=<?= htmlspecialchars($match_id) ?>" method="post" onsubmit="return confirmBet()">
                    <label for="team_id">Équipe :</label>
                    <select id="team_id" name="team_id" required>
                        <option value="<?= htmlspecialchars($match['team1']) ?>"><?= htmlspecialchars($match['team1_name']) ?></option>
                        <option value="<?= htmlspecialchars($match['team2']) ?>"><?= htmlspecialchars($match['team2_name']) ?></option>
                    </select>

                    <label for="amount">Montant :</label>
                    <input type="number" id="amount" name="amount" required step="0.0" min="0.0">

                    <button type="submit"><?= $existing_bet ? 'Actualiser ma mise' : 'Valider ma sélection' ?></button>
                </form>
            </section>
        </section>
    </main>

    <script>
    function confirmBet() {
        var team = document.getElementById('team_id').options[document.getElementById('team_id').selectedIndex].text;
        var amount = document.getElementById('amount').value;
        var message = "Êtes-vous sûr de vouloir " + (<?= json_encode($existing_bet) ?> ? 'actualiser' : 'parier') + " " + amount + " € sur " + team + " ?";
        return confirm(message);
    }
    </script>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
