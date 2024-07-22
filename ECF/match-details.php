<?php
include('includes/db.php');
include('templates/header.php');

// Vérifier si un ID de match est passé dans l'URL
if (isset($_GET['id'])) {
    $match_id = $_GET['id'];

    // Récupérer les détails du match de la base de données
    $stmt = $db->prepare("SELECT * FROM matches WHERE id = :id");
    $stmt->execute(['id' => $match_id]);
    $match = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($match) {
        // Récupérer les joueurs des équipes
        $team1_players = $db->query("SELECT * FROM players WHERE team_id = " . $match['team1_id'])->fetchAll(PDO::FETCH_ASSOC);
        $team2_players = $db->query("SELECT * FROM players WHERE team_id = " . $match['team2_id'])->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Match non trouvé.";
        exit;
    }
} else {
    echo "Aucun match spécifié.";
    exit;
}
?>

<div class="container">
    <h1>Détails du match</h1>
    <h2><?php echo htmlspecialchars($match['team1']) . " vs " . htmlspecialchars($match['team2']); ?></h2>
    <p><strong>Date :</strong> <?php echo htmlspecialchars($match['date']); ?></p>
    <p><strong>Heure de début :</strong> <?php echo htmlspecialchars($match['start_time']); ?></p>
    <p><strong>Heure de fin :</strong> <?php echo htmlspecialchars($match['end_time']); ?></p>
    <p><strong>Statut :</strong> <?php echo htmlspecialchars($match['status']); ?></p>
    <p><strong>Météo :</strong> <?php echo htmlspecialchars($match['weather']); ?></p>

    <h3>Composition des équipes</h3>
    <h4><?php echo htmlspecialchars($match['team1']); ?></h4>
    <ul>
        <?php foreach ($team1_players as $player) : ?>
            <li><?php echo htmlspecialchars($player['first_name'] . " " . $player['last_name'] . " (N°" . $player['number'] . ")"); ?></li>
        <?php endforeach; ?>
    </ul>

    <h4><?php echo htmlspecialchars($match['team2']); ?></h4>
    <ul>
        <?php foreach ($team2_players as $player) : ?>
            <li><?php echo htmlspecialchars($player['first_name'] . " " . $player['last_name'] . " (N°" . $player['number'] . ")"); ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Commentaires et score</h3>
    <p><?php echo htmlspecialchars($match['comments']); ?></p>
    <p><strong>Score :</strong> <?php echo htmlspecialchars($match['score']); ?></p>

    <?php if ($match['status'] == 'À venir') : ?>
        <a href="place-bet.php?id=<?php echo $match['id']; ?>" class="btn btn-primary">Miser</a>
    <?php endif; ?>
</div>

<?php include('templates/footer.php'); ?>
