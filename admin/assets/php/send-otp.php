<?php
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

// Generate 6-digit OTP
$otp = rand(100000, 999999);

// Update OTP in table
$update = $conn->prepare("UPDATE admin_registration SET otp = ? WHERE email = ?");
$update->bind_param("ss", $otp, $email);
$update->execute();

// Send email
$subject = "Your OTP for Password Reset";
$message = "Hello,\n\nYour OTP for password reset is: $otp\n\nPlease do not share this code.";
$headers = "From: no-reply@yourdomain.com";

if (mail($email, $subject, $message, $headers)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP email.']);
}
?>
