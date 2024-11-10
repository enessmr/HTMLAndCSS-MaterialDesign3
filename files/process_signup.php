<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST ile gelen istekleri işleyin
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    // Devam edin...
} else {
    // Eğer POST dışındaki bir yöntemle istek yapılırsa
    header('HTTP/1.1 405 Method Not Allowed');
    echo '405 Method Not Allowed';
    exit;
}

// Veritabanı bağlantısı
$pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');

// Formdan gelen e-posta adresini al
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

// Doğrulama token'ı oluşturma
$activationToken = bin2hex(random_bytes(32));

// Doğrulama URL'si oluşturma
$verificationUrl = 'https://example.com/verify.php?token=' . urlencode($activationToken);

// Veritabanına token'ı kaydetme (örnek tablo adı: users)
$stmt = $pdo->prepare('UPDATE users SET activation_token = :token WHERE email = :email');
$stmt->execute(['token' => $activationToken, 'email' => $email]);

// PHPMailer kullanarak e-posta gönderimi
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // Sunucu ayarları
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com';
    $mail->Password   = 'your_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Alıcı
    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress($email);  // Burada kullanıcının e-posta adresini kullanıyoruz

    // İçerik
    $mail->isHTML(true);
    $mail->Subject = 'Please verify your email address';
    $mail->Body    = 'Click the link to verify your email: <a href="' . $verificationUrl . '">' . $verificationUrl . '</a>';
    
    // E-posta gönderme
    $mail->send();
    echo 'Verification email has been sent.';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
