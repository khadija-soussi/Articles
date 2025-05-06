<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('session.cookie_samesite', 'Lax'); 
ini_set('session.cookie_secure', 'Off'); // Set to 'On' if using HTTPS
session_start();


$timeout_duration = 1800;
file_put_contents("log.txt", print_r($_SESSION, true));
if (!isset($_SESSION['username']) || (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout_duration)) {
    http_response_code(401);
    exit();
}
$_SESSION['last_activity'] = time();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=projet web", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("SELECT username, profile_picture, cover_picture FROM user WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
        exit();
    }

    header("Content-Type: application/json");
    echo json_encode([
        'username' => $user['username'],
        'profile_picture' => $user['profile_picture'],
        'background_picture' => $user['cover_picture']  // match front-end usage
    ]);
    
}catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
