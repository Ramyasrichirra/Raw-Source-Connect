<?php
include '../db.php';
session_start();

$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role']; // get selected role

$sql = "SELECT * FROM users WHERE email = ? AND role = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $role);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
  if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    if ($user['role'] == 'supplier') {
      header("Location: ../supplier/products.php");
    } else {
      header("Location: ../browse.php");
    }
    exit;
  }
}

echo "Invalid credentials or role mismatch.";
?>
