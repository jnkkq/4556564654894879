<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header('Location: login.php');
    exit();
}

include '../includes/db.php';

$match_id = isset($_POST['match_id']) ? (int)$_POST['match_id'] : 0;
$comment = isset($_POST['comment']) ? $_POST['comment'] : '';

if ($match_id <= 0 || empty($comment)) {
    echo "Données invalides.";
    exit();
}

$author = $_SESSION['user_id'];

$query_insert_comment = 'INSERT INTO comments (match_id, author, text) VALUES (:match_id, :author, :text)';
$stmt_insert_comment = $pdo->prepare($query_insert_comment);
$stmt_insert_comment->execute([
    'match_id' => $match_id,
    'author' => $author,
    'text' => $comment
]);

header('Location: commentator.php');
exit();
?>
