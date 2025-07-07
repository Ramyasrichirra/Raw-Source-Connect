<?php
include '../db.php';
session_start();

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

// Insert new user
$sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $password, $role);

if ($stmt->execute()) {
    // Get inserted user ID
    $user_id = $stmt->insert_id;

    // Start session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $role;

    // Redirect by role
    if ($role == 'supplier') {
        header("Location: ../supplier_profile.php");
    } else {
        header("Location: ../browse.php");
    }
    exit;
} else {
    echo "Signup failed: " . $stmt->error;
}
?>
