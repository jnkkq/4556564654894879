<?php
include '../includes/db.php';
include '../includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Requête SQL pour récupérer les informations de l'utilisateur
$query = 'SELECT * FROM users WHERE id = :user_id';
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de l'utilisateur</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <img src="../img/warn-anj.png" alt="Bannière" class="banner-image">
    </header>

    <main class="content">
        <div class="profile-container">
            <h1>Profil de <?= htmlspecialchars($user['username']) ?></h1>
            <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>

            <h2>Options du compte</h2>
            <ul class="account-options">
                <li><a href="bet_history.php">Historique des mises</a></li> 

                <?php if ($user['role'] === 'admin'): ?>
                    <li><a href="admin.php">Administration</a></li>
                <?php elseif ($user['role'] === 'commentateur'): ?>
                    <li><a href="commentateur.php">Page Commentateur</a></li>
                <?php endif; ?>
                <!-- Ajoutez d'autres liens pertinents pour d'autres rôles si nécessaire -->
            </ul>
        </div>
    </main>

    <footer>
        <p>© 2024 Mon Site. Tous droits réservés.</p>
    </footer>
</body>
</html>
