<?php
$password = 'password1';  // Remplacez par le mot de passe souhaité
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>
