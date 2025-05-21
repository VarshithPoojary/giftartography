<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../../PHPMailer/PHPMailer.php';
require '../../../PHPMailer/SMTP.php';
require '../../../PHPMailer/Exception.php';

include 'connection.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required.']);
    exit;
}

// Check if email exists
$stmt = $conn->prepare("SELECT id FROM admin_registration WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email not found.']);
    exit;
}

// Generate OTP
$otp = rand(100000, 999999);
$update = $conn->prepare("UPDATE admin_registration SET otp = ? WHERE email = ?");
$update->bind_param("ss", $otp, $email);
$update->execute();

// Send OTP via Gmail SMTP
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'varshithpoojary16@gmail.com'; // your Gmail
    $mail->Password = 'obyw xptq jrzh mzjq';   // app password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('your-email@gmail.com', 'Giftartography');
    $mail->addAddress($email);
    $mail->Subject = 'Your OTP for Password Reset';
    $mail->Body = "Hello,\n\nYour OTP for password reset is: $otp\n\nPlease do not share this code.";

    $mail->send();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
?>
