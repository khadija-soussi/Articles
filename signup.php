<?php
// Configuration de la base de données
$db_host = 'localhost';
$db_name = 'projet web';
$db_user = 'root';
$db_pass = '';

try {
    // Connexion à la base de données avec PDO
    $db = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        // Récupération des données du formulaire
        $username = $_POST['username'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $genre = $_POST['genre'];
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];
        
        // Vérification des mots de passe
        if ($password != $confirm_password) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }

        // Formatage de la date de naissance
        $date_naissance = "$year-$month-$day";

        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Préparation de la requête SQL avec PDO
        $stmt = $db->prepare("INSERT INTO user (username, password, mail, date_de_naissance, genre, profile_picture, cover_picture) VALUES (:username, :password, :mail, :date_naissance, :genre, '', '')");
        
        // Exécution avec les paramètres nommés
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':mail' => $mail,
            ':date_naissance' => $date_naissance,
            ':genre' => $genre
        ]);

        if ($stmt->rowCount() > 0) {
            // Redirection après inscription réussie
            header("Location: connexion.php?success=1");
            exit();
        } else {
            throw new Exception("Erreur lors de l'inscription.");
        }
    }
} catch (Exception $e) {
    // Gestion des erreurs
    die('Erreur : ' . $e->getMessage());
}
?>