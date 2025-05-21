<?php
include 'connection.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$otp = $_POST['otp'] ?? '';

if (empty($email) || empty($otp)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and OTP are required.']);
    exit;
}

// Check OTP in database
$stmt = $conn->prepare("SELECT id FROM admin_registration WHERE email = ? AND otp = ?");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid OTP or email.']);
}
?>
