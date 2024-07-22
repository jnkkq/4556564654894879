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

$user_id = $_SESSION['user_id'];

// Requête SQL pour récupérer l'historique des paris de l'utilisateur
$query = 'SELECT 
            b.*, 
            m.team1 AS match_team1, 
            m.team2 AS match_team2, 
            m.date, 
            m.start_time, 
            m.end_time, 
            m.status AS match_status, 
            m.score AS match_score, 
            m.odds_team1, 
            m.odds_team2, 
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

/*
Explication des jointures :
- `JOIN matches m ON b.match_id = m.id` : Cette jointure relie la table `bets` à la table `matches` en utilisant l'identifiant du match. Ainsi, nous récupérons les détails du match pour chaque pari.
- `JOIN teams t1 ON m.team1 = t1.id` : Cette jointure relie la table `matches` à la table `teams` pour obtenir les détails de la première équipe (team1) du match.
- `JOIN teams t2 ON m.team2 = t2.id` : Cette jointure relie la table `matches` à la table `teams` pour obtenir les détails de la deuxième équipe (team2) du match.
*/

$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$bets = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main class="content">

    <table>
        <thead>
            <tr>
                <th>Match</th>
                <th>Équipe choisie</th>
                <th>Montant</th>
                <th>Date du pari</th>
                <th>Statut du match</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($bets): ?>
                <?php foreach ($bets as $bet): ?>
                    <tr>
                        <td><?= htmlspecialchars($bet['team1_name']) ?> vs <?= htmlspecialchars($bet['team2_name']) ?></td>
                        <td>
                            <?php
                            // Déterminez quelle équipe a été choisie en comparant l'identifiant de l'équipe du pari avec les équipes du match
                            $team_chosen = $bet['team_id'] == $bet['match_team1'] ? $bet['team1_name'] : $bet['team2_name'];
                            echo htmlspecialchars($team_chosen);
                            ?>
                        </td>
                        <td><?= htmlspecialchars($bet['amount']) ?> €</td>
                        <td><?= htmlspecialchars($bet['created_at']) ?></td>
                        <!-- Affichez le statut du match et le score du match -->
                        <td><?= htmlspecialchars($bet['match_status']) ?></td>
                        <td><?= htmlspecialchars($bet['match_score']) ?></td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">Aucun pari trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include '../includes/footer.php'; ?>
