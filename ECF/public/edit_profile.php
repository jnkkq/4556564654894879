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
$message = "";

// Récupérer les informations actuelles de l'utilisateur
$query = 'SELECT username, email FROM users WHERE id = :user_id';
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    
    if ($password !== $password_confirm) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        // Mettre à jour les informations de l'utilisateur
        if (!empty($password)) {
            // Si le mot de passe est fourni, le hacher avant de le stocker
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = 'UPDATE users SET username = :username, email = :email, password = :password WHERE id = :user_id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'user_id' => $user_id
            ]);
        } else {
            // Sinon, mettre à jour sans changer le mot de passe
            $query = 'UPDATE users SET username = :username, email = :email WHERE id = :user_id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'user_id' => $user_id
            ]);
        }
        $message = "Informations du profil mises à jour avec succès.";
    }
}

?>

<main>
    <h1>Modifier le profil</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form action="edit_profile.php" method="post">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="password">Nouveau mot de passe (laisser vide pour ne pas changer) :</label>
        <input type="password" id="password" name="password">

        <label for="password_confirm">Confirmer le nouveau mot de passe :</label>
        <input type="password" id="password_confirm" name="password_confirm">

        <button type="submit">Mettre à jour</button>
    </form>
</main>

<?php include '../includes/footer.php'; ?>
