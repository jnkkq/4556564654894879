<?php
include '../includes/db.php';
include '../includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $query = 'SELECT * FROM users WHERE username = :username';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: profile.php');
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <main class="content">
        <div class="form-container"> 
            <?php if (!empty($error)) : ?>
                <p class="message error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="login.php" method="post" class="login-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <button type="submit" class="button">Se connecter</button>
                </div>
                <div class="form-group">
                    <a href="forgot_password.php" class="forgot-password">Mot de passe oublié ?</a>
                </div>
            </form>
        </div>
    </main>
</body>
<?php include '../includes/footer.php';?>
</html>
