<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db_host = 'localhost';
$db_name = 'projet_web';
$db_port = '3312';
$db_user = 'root';
$db_pass = '';

$maxFileSize = 2 * 1024 * 1024;
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];

try {
    $db = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $date = $_POST['date'] ?? '';
        $topic = $_POST['topic'] ?? '';
        $content = $_POST['content'] ?? '';
        $username = $_SESSION['username'] ?? 'anonymous';

        if (empty($title) || empty($author) || empty($date) || empty($topic) || empty($content)) {
            header("Location: add_article.php?error=" . urlencode("Tous les champs sont obligatoires."));
            exit();
        }

        if (!DateTime::createFromFormat('Y-m-d', $date)) {
            header("Location: add_article.php?error=" . urlencode("Date invalide."));
            exit();
        }

       
        $imagePath = null;
        if (!empty($_FILES['image']['tmp_name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);

            if (!isset($allowedTypes[$mime])) {
                header("Location: add_article.php?error=" . urlencode("Type d'image non autorisé."));
                exit();
            }

            if ($file['size'] > $maxFileSize) {
                header("Location: add_article.php?error=" . urlencode("Image trop grande (max 2 Mo)."));
                exit();
            }

            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $filename = uniqid('img_', true) . '.' . $allowedTypes[$mime];
            $imagePath = $uploadDir . $filename;

            move_uploaded_file($file['tmp_name'], $imagePath);
        }

        
        $stmt = $db->prepare("
            INSERT INTO articles (title, author, date, topic, content, image_path, username)
            VALUES (:title, :author, :date, :topic, :content, :image_path, :username)
        ");

        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':date' => $date,
            ':topic' => $topic,
            ':content' => $content,
            ':image_path' => $imagePath,
            ':username' => $username
        ]);

        header("Location: index.html?message=" . urlencode("Article publié avec succès !"));
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
    header("Location: add_article.php?error=" . urlencode("Une erreur est survenue."));
    exit();
}
?>