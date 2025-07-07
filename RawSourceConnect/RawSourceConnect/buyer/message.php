<?php
include '../db.php';
session_start();

$from = $_SESSION['user_id'];
$to = $_POST['to_supplier_id'];
$subject = $_POST['subject'];
$message = $_POST['message'];

$sql = "INSERT INTO messages (from_user_id, to_supplier_id, subject, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $from, $to, $subject, $message);
$stmt->execute();

header("Location: ../profile.php?id=$to");
?>
