<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: login.php");
    exit;
}

$supplierId = $_SESSION['user_id'];

// Fetch supplier info
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE userId = ?");
$stmt->bind_param("i", $supplierId);
$stmt->execute();
$supplier = $stmt->get_result()->fetch_assoc();

if (!$supplier) {
    echo "Supplier profile not found.";
    exit;
}

// Fetch products
$productStmt = $conn->prepare("SELECT * FROM products WHERE supplierId = ?");
$productStmt->bind_param("i", $supplierId);
$productStmt->execute();
$products = $productStmt->get_result();

// Fetch messages
$msgStmt = $conn->prepare("SELECT m.*, u.name as buyerName FROM messages m JOIN users u ON m.fromUserId = u.id WHERE m.toSupplierId = ? ORDER BY m.timestamp DESC");
$msgStmt->bind_param("i", $supplierId);
$msgStmt->execute();
$messages = $msgStmt->get_result();

// Fetch reviews
$reviewStmt = $conn->prepare("SELECT r.*, u.name as buyerName FROM reviews r JOIN users u ON r.buyerId = u.id WHERE r.supplierId = ? ORDER BY r.date DESC");
$reviewStmt->bind_param("i", $supplierId);
$reviewStmt->execute();
$reviews = $reviewStmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Supplier Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f8f9fa; }
    .container { max-width: 1100px; margin: 30px auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    h2 { color: #2a9d8f; margin-bottom: 20px; }
    section { margin-bottom: 40px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #2a9d8f; color: white; }
    .btn { background: #2a9d8f; color: white; padding: 7px 12px; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; }
    .btn:hover { background: #217a73; }
    nav { background: #2a9d8f; padding: 12px; text-align: right; }
    nav a { color: white; margin-left: 15px; text-decoration: none; }
    nav a:hover { text-decoration: underline; }
  </style>
</head>
<body>

<nav>
  <a href="supplier_dashboard.php">Dashboard</a>
  <a href="add_product.php">Add Product</a>
  <a href="logout.php">Logout</a>
</nav>

<div class="container">

  <h2>Welcome, <?= htmlspecialchars($supplier['description'] ?: 'Supplier') ?></h2>

  <section>
    <h3>Your Products</h3>
    <?php if ($products->num_rows > 0): ?>
      <table>
        <tr><th>Name</th><th>Description</th><th>Image</th><th>Actions</th></tr>
        <?php while ($product = $products->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['description']) ?></td>
            <td>
              <?php if ($product['image']): ?>
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image" style="width:60px;height:60px;object-fit:cover;">
              <?php else: ?>
                No image
              <?php endif; ?>
            </td>
            <td>
              <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn">Edit</a>
              <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No products added yet.</p>
    <?php endif; ?>
  </section>

  <section>
    <h3>Buyer Messages</h3>
    <?php if ($messages->num_rows > 0): ?>
      <table>
        <tr><th>From</th><th>Subject</th><th>Message</th><th>Date</th></tr>
        <?php while ($msg = $messages->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($msg['buyerName']) ?></td>
            <td><?= htmlspecialchars($msg['subject']) ?></td>
            <td><?= htmlspecialchars(substr($msg['message'], 0, 50)) ?><?= strlen($msg['message']) > 50 ? "..." : "" ?></td>
            <td><?= htmlspecialchars($msg['timestamp']) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No messages from buyers.</p>
    <?php endif; ?>
  </section>

  <section>
    <h3>Ratings & Reviews</h3>
    <?php if ($reviews->num_rows > 0): ?>
      <table>
        <tr><th>Buyer</th><th>Rating</th><th>Comment</th><th>Date</th></tr>
        <?php while ($review = $reviews->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($review['buyerName']) ?></td>
            <td><?= str_repeat('⭐', $review['rating']) ?></td>
            <td><?= htmlspecialchars($review['comment']) ?></td>
            <td><?= htmlspecialchars($review['date']) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p>No reviews received yet.</p>
    <?php endif; ?>
  </section>

</div>

</body>
</html>
