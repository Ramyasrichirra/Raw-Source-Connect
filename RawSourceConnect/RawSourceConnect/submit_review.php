<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyerId = $_SESSION['user_id'];
    $supplierId = intval($_POST['supplierId']);
    $rating = intval($_POST['rating']);
    $comment = $_POST['comment'];
    $date = date('Y-m-d');

    // Insert review
    $stmt = $conn->prepare("INSERT INTO reviews (supplierId, buyerId, rating, comment, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $supplierId, $buyerId, $rating, $comment, $date);
    $stmt->execute();

    // Update averageRating
    $avgStmt = $conn->prepare("SELECT AVG(rating) as avgRating FROM reviews WHERE supplierId = ?");
    $avgStmt->bind_param("i", $supplierId);
    $avgStmt->execute();
    $avgResult = $avgStmt->get_result()->fetch_assoc();
    $avgRating = round($avgResult['avgRating'], 2);

    $updateStmt = $conn->prepare("UPDATE suppliers SET averageRating = ? WHERE userId = ?");
    $updateStmt->bind_param("di", $avgRating, $supplierId);
    $updateStmt->execute();

    header("Location: supplier_profile.php?id=$supplierId");
}
