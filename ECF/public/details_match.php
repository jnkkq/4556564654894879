<?php
// Inclusion du fichier de connexion à la base de données et du fichier d'en-tête
include '../includes/db.php';
include '../includes/header.php';

// Démarrer la session pour accéder aux informations de l'utilisateur
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'identifiant du match
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Requête SQL pour récupérer les détails du match avec les noms des équipes et les cotes
$query = '
    SELECT m.*, t1.name AS team1_name, t2.name AS team2_name
    FROM matches m
    JOIN teams t1 ON m.team1 = t1.id
    JOIN teams t2 ON m.team2 = t2.id
    WHERE m.id = :id
';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    echo "Match non trouvé.";
    exit();
}

// Requête SQL pour récupérer les joueurs des équipes impliquées dans le match
$query_team1 = 'SELECT * FROM players WHERE team_id = :team1_id';
$stmt_team1 = $pdo->prepare($query_team1);
$stmt_team1->execute(['team1_id' => $match['team1']]);
$players_team1 = $stmt_team1->fetchAll(PDO::FETCH_ASSOC);

$query_team2 = 'SELECT * FROM players WHERE team_id = :team2_id';
$stmt_team2 = $pdo->prepare($query_team2);
$stmt_team2->execute(['team2_id' => $match['team2']]);
$players_team2 = $stmt_team2->fetchAll(PDO::FETCH_ASSOC);

// Initialiser le tableau des paris
$bets = [];

if (isset($_SESSION['user_id'])) {
    // Requête SQL pour vérifier s'il y a des paris en cours pour ce match de l'utilisateur connecté
    $query_bets = 'SELECT * FROM bets WHERE match_id = :match_id AND user_id = :user_id';
    $stmt_bets = $pdo->prepare($query_bets);
    $stmt_bets->execute(['match_id' => $id, 'user_id' => $_SESSION['user_id']]);
    $bets = $stmt_bets->fetchAll(PDO::FETCH_ASSOC);
}

// Traitement de la mise à jour d'une mise
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_bet']) && isset($_SESSION['user_id'])) {
    $bet_id = $_POST['bet_id'];
    $new_amount = $_POST['amount'];

    // Vérifiez si la mise appartient bien à l'utilisateur
    $check_bet_query = 'SELECT * FROM bets WHERE id = :bet_id AND user_id = :user_id';
    $stmt_check_bet = $pdo->prepare($check_bet_query);
    $stmt_check_bet->execute(['bet_id' => $bet_id, 'user_id' => $_SESSION['user_id']]);
    $existing_bet = $stmt_check_bet->fetch(PDO::FETCH_ASSOC);

    if ($existing_bet) {
        // Mettre à jour la mise
        $update_bet_query = 'UPDATE bets SET amount = :amount WHERE id = :bet_id';
        $stmt_update_bet = $pdo->prepare($update_bet_query);
        $stmt_update_bet->execute(['amount' => $new_amount, 'bet_id' => $bet_id]);

        // Ajouter une variable de session pour afficher l'alerte
        $_SESSION['alert'] = "Mise mise à jour avec succès!";
        // Réinitialiser le formulaire pour rester sur la même page
        echo '<script>
                window.onload = function() {
                    document.getElementById("alert-message").style.display = "block";
                }
              </script>';
    } else {
        echo "Paris non trouvé ou accès non autorisé.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Match</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Lien vers le fichier CSS -->
    <style>
        /* Style pour les alertes */
        .alert {
            background-color: #d4edda; /* Vert clair */
            color: #155724; /* Vert foncé */
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: none; /* Cacher par défaut */
        }
        /* Style pour le formulaire de mise */
 
        .bet-form input, .bet-form button {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <main class="content">
        <div class="match-details">
            <div id="details-match">
                <h1>Détails du match</h1>
                <h2 id="match-teams"><?= htmlspecialchars($match['team1_name']) ?> vs <?= htmlspecialchars($match['team2_name']) ?></h2>
                <p id="composition-title">Date: <?= htmlspecialchars($match['date']) ?></p>
                <br>
                <p id="match-time">Heure: <?= htmlspecialchars($match['start_time']) ?> à <?= htmlspecialchars($match['end_time']) ?></p>
                <p id="match-status">Status: <?= htmlspecialchars($match['status']) ?></p>
                <p id="match-weather">Météo: <?= htmlspecialchars($match['weather']) ?></p>
                
                <!-- Affichage des cotes -->
                <p id="odds-team1">Cote <?= htmlspecialchars($match['team1_name']) ?>: <?= htmlspecialchars($match['odds_team1']) ?></p>
                <p id="odds-team2">Cote <?= htmlspecialchars($match['team2_name']) ?>: <?= htmlspecialchars($match['odds_team2']) ?></p>
                <br>
                <h3 id="composition-title">Composition des équipes</h3>
                <br>
                <!-- Affichage des joueurs de la première équipe -->
                <h4 id="composition-title"><?= htmlspecialchars($match['team1_name']) ?></h4>
                <ul id="team1-players">
                    <?php foreach ($players_team1 as $player) : ?>
                        <li><?= htmlspecialchars($player['first_name'] . ' ' . $player['last_name']) ?> - #<?= htmlspecialchars($player['number']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <br>
                <!-- Affichage des joueurs de la deuxième équipe -->
                <h4 id="composition-title"><?= htmlspecialchars($match['team2_name']) ?></h4>
                <ul id="team2-players">
                    <?php foreach ($players_team2 as $player) : ?>
                        <li><?= htmlspecialchars($player['first_name'] . ' ' . $player['last_name']) ?> - #<?= htmlspecialchars($player['number']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <br>
                <h3 id="composition-title">Commentaires</h3>
                <p id="comments"><?= nl2br(htmlspecialchars($match['comments'])) ?></p>
                
                <br>
                <h3 id="composition-title">Score</h3>
                <p id="score"><?= htmlspecialchars($match['score']) ?></p>

                <!-- Affichage des paris en cours -->
                <br>
                <h3 id="composition-title">Vos paris en cours</h3>
                
                <?php if (count($bets) > 0) : ?>
                    
                    <ul id="bets-list">
                        <?php foreach ($bets as $bet) : ?>
                            <li>
                                Vous avez parié <?= htmlspecialchars($bet['amount']) ?>€ sur <?= htmlspecialchars($bet['team_id'] == $match['team1'] ? $match['team1_name'] : $match['team2_name']) ?>
                                <!-- Formulaire pour modifier la mise -->
                                <form method="post" class="bet-form">
                                    <input type="hidden" name="bet_id" value="<?= htmlspecialchars($bet['id']) ?>">
                                    <input type="number" name="amount" value="<?= htmlspecialchars($bet['amount']) ?>" step="0.00" min="0" required>
                                    <button type="submit" name="update_bet">Modifier</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Pas de paris en cours</p>
                <?php endif; ?>

                <!-- Affichage du bouton de pari si l'utilisateur est connecté -->
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <?php if ($match['status'] === 'À venir') : ?>
                        <a href="bet.php?id=<?= htmlspecialchars($match['id']) ?>" class="button-doré">Miser</a>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Affichage du message d'alerte si présent -->
                <?php if (isset($_SESSION['alert'])): ?>
                    <div id="alert-message" class="alert">
                        <?= htmlspecialchars($_SESSION['alert']) ?>
                        <?php unset($_SESSION['alert']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
