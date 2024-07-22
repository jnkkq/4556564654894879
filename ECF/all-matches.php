<?php
include('includes/db.php');
include('templates/header.php');

// Récupérer tous les matchs de la base de données
$query = $db->query("SELECT * FROM matches ORDER BY date ASC");
$matches = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Visualiser tous les matchs</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Équipe 1</th>
                <th>Équipe 2</th>
                <th>Date</th>
                <th>Heure de début</th>
                <th>Heure de fin</th>
                <th>Statut</th>
                <th>Score</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($matches as $match) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($match['team1']); ?></td>
                    <td><?php echo htmlspecialchars($match['team2']); ?></td>
                    <td><?php echo htmlspecialchars($match['date']); ?></td>
                    <td><?php echo htmlspecialchars($match['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($match['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($match['status']); ?></td>
                    <td><?php echo htmlspecialchars($match['score']); ?></td>
                    <td>
                        <a href="match-details.php?id=<?php echo $match['id']; ?>" class="btn btn-info">Détails</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('templates/footer.php'); ?>
