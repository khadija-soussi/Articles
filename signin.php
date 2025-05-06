<?php
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', 'Off');
session_start();


$host = "localhost";
$dbname = "projet_web"; // Replace this
$username = "root"; // Change if needed
$password = "";     // Change if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (!empty($email) && !empty($pass)) {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE mail = ?");

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['username'] = $user['username'];  
            $_SESSION['last_activity'] = time();


            header("Location: profile.html");
        } else {
            echo "Invalid credentials.";
        }
    } else {
        echo "Please fill in both fields.";
    }
} else {
    echo "Invalid request method.";
}
?>