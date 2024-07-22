<?php
 
include '../includes/db.php';
include '../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier si le nom d'utilisateur existe déjà
        $query = 'SELECT * FROM users WHERE username = :username';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user) {
            $error = 'Le nom d\'utilisateur est déjà pris.';
        } else {
            // Insérer le nouvel utilisateur dans la base de données
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $query = 'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ]);
            $success = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
        }
    }
}
?>

<main class="content">
    <div class="form-container">
        
        <?php if ($error): ?>
            <p class="message error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="message success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        <form method="post" action="register.php" class="register-form">
            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <button type="submit" class="button">S'inscrire</button>
            </div>
        </form>
    </div>
</main>

<?php include '../includes/footer.php';?>
