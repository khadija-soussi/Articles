<?php
$host = "localhost";
$dbname = "projet web";
$user = "root";
$password = "";

// Connexion
$conn = new mysqli($host, $user, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// echo "Connexion réussie à la base de données";
?>
