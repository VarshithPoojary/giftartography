<?php
include 'connection.php';
header('Content-Type: application/json');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jsonData = json_decode($_POST['json'], true);

    if (!$jsonData) {
        echo json_encode(["success" => false, "error" => "Invalid JSON"]);
        exit;
    }

    $username = $jsonData["username"];
    $email = $jsonData["email"];
    $phone = $jsonData["phone"];
    $password = $jsonData["password"];
    $type = $jsonData["type"];

    $profileImage = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $uploadDir = "uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $fileName = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
            $profileImage = $targetFile;
        }
    }
 
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Save to database (example query, replace with your table/fields)
    $stmt = $conn->prepare("INSERT INTO admin_registration (name, email, phone, password, type, profile_image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $username, $email, $phone, $hashedPassword, $type, $profileImage);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "DB Error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
