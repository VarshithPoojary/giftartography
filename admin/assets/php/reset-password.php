<?php
include 'connection.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';

if (empty($email) || empty($newPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and new password are required.']);
    exit;
}

// Hash new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update in database
$stmt = $conn->prepare("UPDATE admin_registration SET password = ?, otp = NULL WHERE email = ?");
$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password.']);
}
?>
