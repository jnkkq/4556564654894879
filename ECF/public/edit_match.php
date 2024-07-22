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

if (!$user || !in_array($user['role'], ['admin', 'commentateur'])) {
    echo "Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.";
    exit();
}

// Récupérer l'ID du match à modifier
$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($match_id === 0) {
    echo "ID de match invalide.";
    exit();
}

// Requête pour récupérer les détails du match
$query = 'SELECT * FROM matches WHERE id = :match_id';
$stmt = $pdo->prepare($query);
$stmt->execute(['match_id' => $match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    echo "Match non trouvé. ID du match : " . htmlspecialchars($match_id);
    exit();
}

// Requête pour récupérer les équipes
$query_teams = 'SELECT * FROM teams';
$stmt_teams = $pdo->prepare($query_teams);
$stmt_teams->execute();
$teams = $stmt_teams->fetchAll(PDO::FETCH_ASSOC);

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team1 = (int)$_POST['team1'];
    $team2 = (int)$_POST['team2'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];
    $score = $_POST['score'];
    $weather = $_POST['weather'];
    $comments = $_POST['comments'];
    $odds_team1 = (float)$_POST['odds_team1'];
    $odds_team2 = (float)$_POST['odds_team2'];

    // Mettre à jour les détails du match
    $query_update = 'UPDATE matches SET
                     team1 = :team1,
                     team2 = :team2,
                     date = :date,
                     start_time = :start_time,
                     end_time = :end_time,
                     status = :status,
                     score = :score,
                     weather = :weather,
                     comments = :comments,
                     odds_team1 = :odds_team1,
                     odds_team2 = :odds_team2
                     WHERE id = :match_id';
    
    $stmt_update = $pdo->prepare($query_update);
    $stmt_update->execute([
        'team1' => $team1,
        'team2' => $team2,
        'date' => $date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'status' => $status,
        'score' => $score,
        'weather' => $weather,
        'comments' => $comments,
        'odds_team1' => $odds_team1,
        'odds_team2' => $odds_team2,
        'match_id' => $match_id
    ]);

    if ($user['role'] === 'admin') {
        header('Location: admin.php');
    } elseif ($user['role'] === 'commentateur') {
        header('Location: commentateur.php');
    }
    exit();
}
?>
<style>
    #team1, #team2 {
        background-color: #24252f;
        color: #ffcc00;
    }
</style>
<main class="content">
    <h1>Modifier le match</h1>
    <form action="edit_match.php?id=<?= htmlspecialchars($match_id) ?>" method="post">
        <label for="team1">Équipe 1:</label>
        <select id="team1" name="team1" required>
            <?php foreach ($teams as $team): ?>
                <option value="<?= htmlspecialchars($team['id']) ?>" <?= $team['id'] == $match['team1'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($team['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="team2">Équipe 2:</label>
        <select id="team2" name="team2" required>
            <?php foreach ($teams as $team): ?>
                <option value="<?= htmlspecialchars($team['id']) ?>" <?= $team['id'] == $match['team2'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($team['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="date">Date:</label>
        <input type="date" id="team1" name="date" value="<?= htmlspecialchars($match['date']) ?>" required>

        <label for="start_time">Heure de début:</label>
        <input type="time" id="team1" name="start_time" value="<?= htmlspecialchars($match['start_time']) ?>" required>

        <label for="end_time">Heure de fin:</label>
        <input type="time" id="team1" name="end_time" value="<?= htmlspecialchars($match['end_time']) ?>" required>

        <label for="status">Statut:</label>
        <input type="text" id="team1" name="status" value="<?= htmlspecialchars($match['status']) ?>" required>

        <label for="score">Score:</label>
        <input type="text" id="team1" name="score" value="<?= htmlspecialchars($match['score']) ?>">

        <label for="weather">Météo:</label>
        <input type="text" id="team1" name="weather" value="<?= htmlspecialchars($match['weather']) ?>">

        <label for="comments">Commentaires:</label>
        <textarea id="team1" name="comments"><?= htmlspecialchars($match['comments']) ?></textarea>

        <label for="odds_team1">Cote équipe 1:</label>
        <input type="number" id="team1" name="odds_team1" value="<?= htmlspecialchars($match['odds_team1']) ?>" step="0.01" required>

        <label for="odds_team2">Cote équipe 2:</label>
        <input type="number" id="team1" name="odds_team2" value="<?= htmlspecialchars($match['odds_team2']) ?>" step="0.01" required>

        <button type="submit" class="team1">Mettre à jour</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
