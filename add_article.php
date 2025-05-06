<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$db_host = 'localhost';
$db_name = 'projet web';  // Database name with space
$db_port = '3306'; 
$db_user = 'root';
$db_pass = '';

// Maximum file size (2MB)
const MAX_FILE_SIZE = 2 * 1024 * 1024;
const ALLOWED_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];

try {
    // Connect to database with UTF-8 - using backticks for database name with space
    $db = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=`$db_name`;charset=utf8mb4",
        $db_user, 
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
        // [Previous code...]
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate input
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $topic = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = "anonymous"; // Default username since session is removed

        // Validate required fields
        if (empty($title) || empty($author) || empty($date) || empty($topic) || empty($content)) {
            header("Location: add_article.php?error=" . urlencode("Tous les champs doivent être remplis!"));
            exit();
        }

        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $date)) {
            header("Location: add_article.php?error=" . urlencode("Format de date invalide!"));
            exit();
        }

        // Handle file upload
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            
            // Validate file
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            
            if (!array_key_exists($mime, ALLOWED_TYPES)) {
                header("Location: add_article.php?error=" . urlencode("Type de fichier non autorisé. Formats acceptés: " . implode(', ', array_values(ALLOWED_TYPES))));
                exit();
            }

            if ($file['size'] > MAX_FILE_SIZE) {
                header("Location: add_article.php?error=" . urlencode("Le fichier est trop volumineux (max 2MB)"));
                exit();
            }

            // Create upload directory if needed
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate secure filename
            $extension = ALLOWED_TYPES[$mime];
            $filename = sprintf('%s.%s', sha1_file($file['tmp_name']), $extension);
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $imagePath = $destination;
            } else {
                header("Location: add_article.php?error=" . urlencode("Erreur lors du téléchargement de l'image"));
                exit();
            }
        }

        // Insert article into database
        $stmt = $db->prepare("
            INSERT INTO articles 
            (title, author, date, topic, content, image_path, username, created_at) 
            VALUES (:title, :author, :date, :topic, :content, :image_path, :username, NOW())
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

        // Redirect with success message
        header("Location: profile.php?success=" . urlencode("Article publié avec succès!"));
        exit();
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    header("Location: add_article.php?error=" . urlencode("Erreur de base de données"));
    exit();
} catch (Exception $e) {
    error_log("General Error: " . $e->getMessage());
    header("Location: add_article.php?error=" . urlencode("Une erreur inattendue est survenue"));
    exit();
}