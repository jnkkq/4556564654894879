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

// Définir la locale en français (fonctionne si le support est disponible)
setlocale(LC_TIME, 'fr_FR.UTF-8');

// Fonction pour obtenir le nom du mois en français
function getMonthName($date) {
    $months = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
        7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
    ];
    return $months[date('n', strtotime($date))];
}

// Fonction pour obtenir le jour de la semaine en français
function getDayOfWeek($date) {
    $days = [
        'Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi', 
        'Friday' => 'Vendredi', 'Saturday' => 'Samedi', 'Sunday' => 'Dimanche'
    ];
    return $days[date('l', strtotime($date))];
}

// Fonction pour obtenir la date complète en français
function getFullDate($date) {
    $dayOfWeek = getDayOfWeek($date);
    $day = date('j', strtotime($date));
    $month = getMonthName($date);
    $year = date('Y', strtotime($date));
    return "$dayOfWeek $day $month $year";
}

// Requête pour récupérer les matchs
$query = '
    SELECT m.*, t1.name AS team1_name, t2.name AS team2_name
    FROM matches m
    JOIN teams t1 ON m.team1 = t1.id
    JOIN teams t2 ON m.team2 = t2.id
    ORDER BY m.date ASC
';
$stmt = $pdo->query($query);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Séparer les matchs en cours et terminés
$upcomingMatches = [];
$pastMatches = [];
$currentDate = date('Y-m-d');

foreach ($matches as $match) {
    if ($match['date'] >= $currentDate) {
        $upcomingMatches[] = $match;
    } else {
        $pastMatches[] = $match;
    }
}

// Requête pour récupérer l'historique des paris
$query_bets = 'SELECT 
                b.*, 
                m.team1 AS match_team1, 
                m.team2 AS match_team2, 
                m.date, 
                m.start_time, 
                m.end_time, 
                m.status AS match_status, 
                m.score AS match_score, 
                t1.name AS team1_name, 
                t2.name AS team2_name 
              FROM 
                bets b 
              JOIN 
                matches m ON b.match_id = m.id 
              JOIN 
                teams t1 ON m.team1 = t1.id 
              JOIN 
                teams t2 ON m.team2 = t2.id 
              WHERE 
                b.user_id = :user_id 
              ORDER BY 
                b.created_at DESC';

$stmt_bets = $pdo->prepare($query_bets);
$stmt_bets->execute(['user_id' => $user_id]);
$bets = $stmt_bets->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Matchs</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <img src="../img/warn-anj.png" alt="Bannière" class="banner-image">
    </header>

    <main class="content">
        <section class="main-content">

            <!-- Section pour les matchs à venir -->
            <h1>Matchs à venir</h1>
            <?php if (count($upcomingMatches) > 0): ?>
                <?php
                $currentMonth = '';
                foreach ($upcomingMatches as $match) :
                    $matchMonth = getMonthName($match['date']); 
                    $matchFullDate = getFullDate($match['date']); 
                    $matchTime = date('H:i', strtotime($match['start_time'])); 

                    if ($currentMonth != $matchMonth) {
                        if ($currentMonth != '') {
                            echo '</ul>';
                        }
                        $currentMonth = $matchMonth;
                        echo "<h2 class='month-header'>$currentMonth</h2><ul>";
                    }
                ?>
                    <li class="match">
                        <a href="details_match.php?id=<?= htmlspecialchars($match['id']) ?>" class="match-link">
                            <div class="match-icon">
                                <img src="../img/balle.png" alt="Icone Ballon de Foot Américain">
                                <span class="tournament"><?= htmlspecialchars($match['league']) ?></span>
                            </div>
                            <div class="match-details">
                                <div class="teams">
                                    <div class="team"><strong><?= htmlspecialchars($match['team1_name']) ?></strong></div>
                                    <div class="vs">vs</div>
                                    <div class="team"><strong><?= htmlspecialchars($match['team2_name']) ?></strong></div>
                                </div>
                                <div class="match-info">
                                    <div class="date-time">
                                        <span class="full-date"><?= htmlspecialchars($matchFullDate) ?></span> <span class="time"><?= $matchTime ?></span>
                                    </div>
                                    <div class="prediction">Qui va gagner le match ? (prolongations incluses)</div>
                                    <div class="odds">
                                        <div class="team-odd"><span class="team-name"><?= htmlspecialchars($match['team1_name']) ?></span> <span class="odd"><?= htmlspecialchars($match['odds_team1']) ?></span></div>
                                        <div class="team-odd"><span class="team-name"><?= htmlspecialchars($match['team2_name']) ?></span> <span class="odd"><?= htmlspecialchars($match['odds_team2']) ?></span></div>
                                    </div>
                                </div>
                                <?php
                                foreach ($bets as $bet) {
                                    if ($bet['match_id'] == $match['id']) {
                                        $teamName = $bet['team_id'] == $match['team1'] ? $match['team1_name'] : $match['team2_name'];
                                        $odds = $bet['team_id'] == $match['team1'] ? $match['odds_team1'] : $match['odds_team2'];
                                        $potentialGain = $bet['amount'] * $odds;
                                        echo "<div class='bet-info'>
                                                <p>Mise : {$bet['amount']} € sur <strong>{$teamName}</strong></p>
                                                <p>Gain Potentiel : $potentialGain €</p>
                                              </div>";
                                    }
                                }
                                ?>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun match à venir.</p>
            <?php endif; ?>

            <!-- Section pour les matchs terminés -->
            <h1>Matchs Terminés</h1>
            <?php if (count($pastMatches) > 0): ?>
                <?php
                $currentMonth = '';
                foreach ($pastMatches as $match) :
                    $matchMonth = getMonthName($match['date']); 
                    $matchFullDate = getFullDate($match['date']); 
                    $matchTime = date('H:i', strtotime($match['start_time'])); 

                    if ($currentMonth != $matchMonth) {
                        if ($currentMonth != '') {
                            echo '</ul>';
                        }
                        $currentMonth = $matchMonth;
                        echo "<h2 class='month-header'>$currentMonth</h2><ul>";
                    }
                ?>
                    <li class="match">
                        <a href="details_match.php?id=<?= htmlspecialchars($match['id']) ?>" class="match-link">
                            <div class="match-icon">
                                <img src="../img/balle.png" alt="Icone Ballon de Foot Américain">
                                <span class="tournament"><?= htmlspecialchars($match['league']) ?></span>
                            </div>
                            <div class="match-details">
                                <div class="teams">
                                    <div class="team"><strong><?= htmlspecialchars($match['team1_name']) ?></strong></div>
                                    <div class="vs">vs</div>
                                    <div class="team"><strong><?= htmlspecialchars($match['team2_name']) ?></strong></div>
                                </div>
                                <div class="match-info">
                                    <div class="date-time">
                                        <span class="full-date"><?= htmlspecialchars($matchFullDate) ?></span> <span class="time"><?= $matchTime ?></span>
                                    </div>
                                    <div class="prediction">Qui a gagné ?</div>
                                    <div class="result">Score : <?= htmlspecialchars($match['score']) ?></div>
                                </div>
                                <?php
                                foreach ($bets as $bet) {
                                    if ($bet['match_id'] == $match['id']) {
                                        $teamName = $bet['team_id'] == $match['team1'] ? $match['team1_name'] : $match['team2_name'];
                                        $odds = $bet['team_id'] == $match['team1'] ? $match['odds_team1'] : $match['odds_team2'];
                                        $gain = $bet['team_id'] == $match['winner'] ? $bet['amount'] * $odds : 0;
                                        echo "<div class='bet-info'>
                                                <p>Mise : {$bet['amount']} € sur <strong>{$teamName}</strong></p>
                                                <p>Gain : $gain €</p>
                                              </div>";
                                    }
                                }
                                ?>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun match terminé.</p>
            <?php endif; ?>
        </section>

        <!-- Colonne gauche pour les matchs terminés -->
        <aside class="left-sidebar">
            <h2>Matchs Terminés</h2>
            <?php if (count($pastMatches) > 0): ?>
                <?php
                foreach ($pastMatches as $match) :
                    $matchFullDate = getFullDate($match['date']);
                ?>
                    <div class="past-match">
                        <a href="details_match.php?id=<?= htmlspecialchars($match['id']) ?>">
                            <div class="match-summary">
                                <div class="teams">
                                    <div class="team"><?= htmlspecialchars($match['team1_name']) ?></div>
                                    <div class="vs">vs</div>
                                    <div class="team"><?= htmlspecialchars($match['team2_name']) ?></div>
                                </div>
                                <div class="match-info">
                                    <div class="date-time"><?= htmlspecialchars($matchFullDate) ?></div>
                                    <div class="result">Score : <?= htmlspecialchars($match['score']) ?></div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun match terminé.</p>
            <?php endif; ?>
        </aside>

        <!-- Colonne droite pour l'historique des paris -->
        <aside class="right-sidebar">
            <h2>Historique des Paris</h2>
            <?php if (count($bets) > 0): ?>
                <?php foreach ($bets as $bet): ?>
                    <div class="bet-card"> 
                        <div class="team">
                            <?= htmlspecialchars($bet['team1_name']) ?> <div class="vs"> vs </div> <?= htmlspecialchars($bet['team2_name']) ?>
                        </div>
                        <br>
                        <div class="match-info">
                            <?php
                            $teamName = $bet['team_id'] == $bet['match_team1'] ? $bet['team1_name'] : $bet['team2_name'];
                            echo htmlspecialchars($teamName);
                            ?>
                        </div>
                        <div class="match-info">
                            <?= htmlspecialchars($bet['amount']) ?> €
                        </div>
                        <div class="match-info">
                            <?= htmlspecialchars($bet['created_at']) ?>
                        </div> 
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun pari trouvé.</p>
            <?php endif; ?>
        </aside>

    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
