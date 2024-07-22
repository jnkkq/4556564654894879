<?php
// Vérifiez si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);

// Vérifiez le rôle de l'utilisateur, si connecté
$user_role = null;
if ($is_logged_in) {
    include '../includes/db.php';
    $user_id = $_SESSION['user_id'];
    $query_role = 'SELECT role FROM users WHERE id = :user_id';
    $stmt_role = $pdo->prepare($query_role);
    $stmt_role->execute(['user_id' => $user_id]);
    $user = $stmt_role->fetch(PDO::FETCH_ASSOC);
    $user_role = $user['role'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Site</title>
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/main.js" defer></script>  
</head>
<body>
 
    <div class="black-bar"></div>
    
    <!-- Image de la bannière -->
    <header>
        <img src="../img/warn-anj.png" alt="Bannière" class="banner-image">
    </header>

    <!-- Container pour la navigation -->
    <div class="header-container">
        <nav>
            <header class="login-banner"> 
                <div class="logo-container">
                    <a href="index.php">
                        <img src="../img/SuperBet-logo.png" alt="Logo de votre site"> 
                        <img src="../img/24920.png" alt="Logo de votre site">
                    </a>
                </div>
                <div class="auth-links">
                    <a href="match.php" class="button">Match</a>
                    <a href="visualiser_match.php" class="button">Miser</a>

                    <?php if ($is_logged_in): ?>
                        <a href="profile.php" class="button">Mon Espace</a>
                        <?php if ($user_role === 'admin'): ?>
                            <a href="admin.php" class="button">ADMIN</a>
                        <?php elseif ($user_role === 'commentator'): ?>
                            <a href="commentator.php" class="button">COMMENTATEUR</a>
                        <?php endif; ?>
                        <a href="logout.php" class="button">Déconnexion</a>
                    <?php else: ?>
                        <a href="login.php" class="button">Connexion</a>
                        <a href="register.php" class="button">Inscription</a>
                    <?php endif; ?>
                </div>
            </header>
        </nav>
    </div>
 

    <script src="../js/main.js" defer></script>  
</body>
</html>
