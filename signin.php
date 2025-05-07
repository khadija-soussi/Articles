<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Database configuration
$db_host = 'localhost';
$db_name = 'projet_web';
$db_port = '3312'; 
$db_user = 'root';
$db_pass = '';

try {
    $db = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8", 
                  $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $identifier = trim($_POST['identifier'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            echo "<script>
            alert('❌ Please fill in all fields.');
            window.location.href = 'sign in.html';
          </script>";
            exit();

        }

        // Determine if it's an email or username
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $stmt = $db->prepare("SELECT * FROM user WHERE mail = :identifier");
        } else {
            $stmt = $db->prepare("SELECT * FROM user WHERE username = :identifier");
        }

        $stmt->execute([':identifier' => $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            header("Location: profile.html"); // redirect to homepage after login
            exit();
        } else {
            header("Location: sign in.html?error=" . urlencode("❌ Invalid username/email or password."));
            exit();
        }
    }
} catch (PDOException $e) {
    echo "<script>
            alert('Database error: " . addslashes($e->getMessage()) . "');
            window.location.href = 'sign in.html';
          </script>";
    exit();
}
?>
