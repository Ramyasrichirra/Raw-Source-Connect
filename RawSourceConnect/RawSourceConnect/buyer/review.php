<?php
include '../db.php';
session_start();

$supplier_id = $_POST['supplier_id'];
$buyer_id = $_SESSION['user_id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

$sql = "INSERT INTO reviews (supplier_id, buyer_id, rating, comment) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiis", $supplier_id, $buyer_id, $rating, $comment);
$stmt->execute();

// Update average rating
$conn->query("UPDATE suppliers SET average_rating = (SELECT AVG(rating) FROM reviews WHERE supplier_id = $supplier_id) WHERE id = $supplier_id");

header("Location: ../profile.php?id=$supplier_id");
?>
