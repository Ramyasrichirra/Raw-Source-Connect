<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from = $_SESSION['user_id'];
    $to = intval($_POST['toSupplierId']);
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $timestamp = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO messages (fromUserId, toSupplierId, subject, message, timestamp) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $from, $to, $subject, $message, $timestamp);
    $stmt->execute();

    header("Location: supplier_profile.php?id=$to");
}
