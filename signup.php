<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['mail'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $gender = $_POST['genre'] ?? '';
        $day = $_POST['day'] ?? '';
        $month = $_POST['month'] ?? '';
        $year = $_POST['year'] ?? '';

        
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            die("Please fill in all required fields.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email format.");
        }

        if ($password !== $confirm_password) {
            die("Passwords do not match.");
        }

        if (strlen($password) < 8) {
            die("Password must be at least 8 characters long.");
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $dob = "$year-$month-$day"; 

        
        $stmt = $db->prepare("
            INSERT INTO user (username, password, mail, date_de_naissance, genre)
            VALUES (:username, :password, :mail, :dob, :genre)
        ");
        

        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':mail' => $email,
            ':dob' => $dob,
            ':genre' => $gender
        ]);
        
$mail = new PHPMailer(true);

try {
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'projetwebensi2025@gmail.com';        
    $mail->Password = 'cbismckaawhmoega';             
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    
    $mail->setFrom('projetwebensi2025@gmail.com', 'Articles Website');
    $mail->addAddress($email, $username);

   
    $mail->isHTML(true);
    $mail->Subject = 'Bienvenue sur notre site';
    $mail->Body = "<h1>Bonjour $username !</h1><p>Votre compte a été créé avec succès.</p>";

    $mail->send();
   
} catch (Exception $e) {
    echo "Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}";
}


        header("Location: sign in.html");
        exit();
    }

} catch (Exception $e) {
    die('❌ Error: ' . $e->getMessage());
}
?>
