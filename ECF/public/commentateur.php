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

if (!$user || $user['role'] !== 'commentateur') {
    echo "Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.";
    exit();
}

// Traitement de la suppression de match
if (isset($_GET['delete_id'])) {
    $match_id = $_GET['delete_id'];
    $delete_query = 'DELETE FROM matches WHERE id = :id';
    $stmt_delete = $pdo->prepare($delete_query);
    $stmt_delete->execute(['id' => $match_id]);

    $_SESSION['alert'] = "Match supprimé avec succès!";
    header('Location: commentateur.php');
    exit();
}

// Traitement du formulaire de création de match
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_match'])) {
    $team1 = $_POST['team1'];
    $team2 = $_POST['team2'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];
    $odds_team1 = $_POST['odds_team1'];
    $odds_team2 = $_POST['odds_team2'];

    $insert_query = 'INSERT INTO matches (team1, team2, date, start_time, end_time, status, odds_team1, odds_team2) VALUES (:team1, :team2, :date, :start_time, :end_time, :status, :odds_team1, :odds_team2)';
    $stmt_insert = $pdo->prepare($insert_query);
    $stmt_insert->execute([
        'team1' => $team1,
        'team2' => $team2,
        'date' => $date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'status' => $status,
        'odds_team1' => $odds_team1,
        'odds_team2' => $odds_team2
    ]);

    $_SESSION['alert'] = "Match créé avec succès!";
    header('Location: commentateur.php');
    exit();
}

// Traitement du formulaire de création d'équipe
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_team'])) {
    $team_name = $_POST['team_name'];
    $team_country = $_POST['team_country'];

    $insert_query = 'INSERT INTO teams (name, country) VALUES (:name, :country)';
    $stmt_insert = $pdo->prepare($insert_query);
    $stmt_insert->execute([
        'name' => $team_name,
        'country' => $team_country
    ]);

    $_SESSION['alert'] = "Équipe créée avec succès!";
    header('Location: commentateur.php');
    exit();
}

// Traitement du formulaire de création de joueur
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_player'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $number = $_POST['number'];
    $team_id = $_POST['team_id'];

    $insert_query = 'INSERT INTO players (first_name, last_name, number, team_id) VALUES (:first_name, :last_name, :number, :team_id)';
    $stmt_insert = $pdo->prepare($insert_query);
    $stmt_insert->execute([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'number' => $number,
        'team_id' => $team_id
    ]);

    $_SESSION['alert'] = "Joueur créé avec succès!";
    header('Location: commentateur.php');
    exit();
}

// Requête pour récupérer les informations nécessaires pour l'commentateuristration avec les noms des équipes
$matches_query = '
    SELECT m.id, t1.name AS team1_name, t2.name AS team2_name, m.date, m.start_time, m.end_time, m.status, m.odds_team1, m.odds_team2
    FROM matches m
    JOIN teams t1 ON m.team1 = t1.id
    JOIN teams t2 ON m.team2 = t2.id
';
$matches_stmt = $pdo->prepare($matches_query);
$matches_stmt->execute();
$matches = $matches_stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les équipes pour les menus déroulants
$teams_query = 'SELECT id, name FROM teams';
$teams_stmt = $pdo->prepare($teams_query);
$teams_stmt->execute();
$teams = $teams_stmt->fetchAll(PDO::FETCH_ASSOC);

// Messages d'alerte
$alertMessage = '';
if (isset($_SESSION['alert'])) {
    $alertMessage = $_SESSION['alert'];
    unset($_SESSION['alert']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>commentateuristration</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        #team1, #team2, #team_name, #team_country {
            background-color: #24252f;
            color: #ffcc00;
        }

        #date, #start_time, #end_time, #status, #odds_team1, #odds_team2,
        #first_name, #last_name, #number, #team_id {
            background-color: #24252f;
            color: #ffcc00;
        }

        .content-commentateur {
            padding: 20px;
        }

        .alert {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #d6e9c6;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <img src="../img/warn-anj.png" alt="Bannière" class="banner-image">
    </header>

    <main class="content-commentateur">
        <?php if ($alertMessage): ?>
            <div class="alert">
                <?= htmlspecialchars($alertMessage) ?>
            </div>
        <?php endif; ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <h2>Liste des Matchs</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Équipe 1</th>
                <th>Équipe 2</th>
                <th>Date</th>
                <th>Heure de début</th>
                <th>Heure de fin</th>
                <th>Status</th>
                <th>Cote Équipe 1</th>
                <th>Cote Équipe 2</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($matches as $match) : ?>
                <tr>
                    <td><?= htmlspecialchars($match['id']) ?></td>
                    <td><?= htmlspecialchars($match['team1_name']) ?></td>
                    <td><?= htmlspecialchars($match['team2_name']) ?></td>
                    <td><?= htmlspecialchars($match['date']) ?></td>
                    <td><?= htmlspecialchars($match['start_time']) ?></td>
                    <td><?= htmlspecialchars($match['end_time']) ?></td>
                    <td><?= htmlspecialchars($match['status']) ?></td>
                    <td><?= htmlspecialchars($match['odds_team1']) ?></td>
                    <td><?= htmlspecialchars($match['odds_team2']) ?></td>
                    <td>
                        <a href="edit_match.php?id=<?= htmlspecialchars($match['id']) ?>">Modifier</a>
                        <a href="commentateur.php?delete_id=<?= htmlspecialchars($match['id']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <br>

<br>
         
     
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
