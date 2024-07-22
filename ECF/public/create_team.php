<?php
// Inclusion du fichier de connexion à la base de données
include '../includes/db.php';

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification si le champ team_name est présent dans la requête POST
    if (isset($_POST['team_name'])) {
        // Récupération du nom de l'équipe depuis le formulaire
        $team_name = $_POST['team_name'];

        // Requête SQL pour insérer une nouvelle équipe dans la base de données
        $query = "INSERT INTO teams (name) VALUES (:team_name)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':team_name', $team_name);

        // Exécution de la requête
        if ($stmt->execute()) {
            // Redirection vers la page d'accueil avec un message de succès
            header("Location: index.php?success=team_created");
            exit();
        } else {
            // En cas d'erreur lors de l'exécution de la requête, affichage d'un message d'erreur
            echo "Erreur lors de la création de l'équipe.";
        }
    } else {
        // Si le champ team_name n'est pas présent dans la requête POST, affichage d'un message d'erreur
        echo "Veuillez fournir un nom d'équipe.";
    }
} else {
    // Si la requête n'est pas de type POST, redirection vers la page d'accueil
    header("Location: index.php");
    exit();
}


?>


