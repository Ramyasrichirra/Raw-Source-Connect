<?php
session_start();
include '../db.php';

// Ensure supplier is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit;
}

$supplierId = $_SESSION['user_id'];

// Fetch reviews and buyer info
$stmt = $conn->prepare("
  SELECT r.rating, r.comment, r.date, u.name as buyer_name
  FROM reviews r
  JOIN users u ON r.buyerId = u.id
  WHERE r.supplierId = ?
  ORDER BY r.date DESC
");
$stmt->bind_param("i", $supplierId);
$stmt->execute();
$reviews = $stmt->get_result();

// Calculate average rating
$avgStmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE supplierId = ?");
$avgStmt->bind_param("i", $supplierId);
$avgStmt->execute();
$avgResult = $avgStmt->get_result()->fetch_assoc();
$avgRating = round($avgResult['avg_rating'], 2);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Supplier Reviews</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f4f3;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
      color: #2a9d8f;
    }

    .review-card {
      background: #f1fdf8;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 10px;
    }

    .review-card h4 {
      margin: 0;
      color: #264653;
    }

    .stars {
      color: #f4a261;
      font-size: 18px;
      margin-top: 5px;
    }

    nav {
      text-align: right;
      padding: 10px;
      background: #2a9d8f;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin: 0 15px;
    }

    nav a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<nav>
  <a href="dashboard.php">Dashboard</a>
  <a href="../logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Reviews & Ratings</h2>
  <p><strong>Average Rating:</strong> <?= $avgRating ?> ⭐</p>

  <?php if ($reviews->num_rows > 0): ?>
    <?php while ($review = $reviews->fetch_assoc()): ?>
      <div class="review-card">
        <h4><?= htmlspecialchars($review['buyer_name']) ?></h4>
        <div class="stars"><?= str_repeat("⭐", $review['rating']) ?></div>
        <p><strong>Comment:</strong> <?= nl2br(htmlspecialchars($review['comment'])) ?></p>
        <p><em><?= $review['date'] ?></em></p>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No reviews yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
