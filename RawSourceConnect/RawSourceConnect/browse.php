<?php
session_start();
include 'db.php';

// Check if buyer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}

// Filters
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$address = isset($_GET['address']) ? $_GET['address'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT s.*, u.name, u.email FROM suppliers s
        JOIN users u ON s.id = u.id
        WHERE 1";

$params = [];
$types = "";

if ($keyword !== "") {
    $sql .= " AND (u.name LIKE ? OR s.description LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $types .= "ss";
}

if ($address !== "") {
    $sql .= " AND s.address LIKE ?";
    $params[] = "%$address%";
    $types .= "s";
}

if ($category !== "") {
    $sql .= " AND s.category = ?";
    $params[] = $category;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Browse Suppliers</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f5;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
      color: #2a9d8f;
      margin-bottom: 20px;
    }

    .filter-form input, .filter-form select {
      padding: 10px;
      margin: 10px 10px 0 0;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .filter-form button {
      padding: 10px 16px;
      background: #2a9d8f;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .supplier-card {
      background: #ecfdf5;
      margin-top: 20px;
      padding: 20px;
      border-radius: 10px;
    }

    .supplier-card h3 {
      margin: 0;
      color: #264653;
    }

    .supplier-card p {
      margin: 5px 0;
    }

    .stars {
      color: #f4a261;
    }

    a.profile-link {
      color: #0077b6;
      text-decoration: none;
    }

    a.profile-link:hover {
      text-decoration: underline;
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
  <a href="logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Find Eco-Friendly Suppliers</h2>

  <form class="filter-form" method="GET">
    
    <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($address) ?>">
    <select name="category">
      <option value="">All Categories</option>
      <option value="Plants" <?= $category == 'Plants' ? 'selected' : '' ?>>Plants</option>
      <option value="Milk" <?= $category == 'Milk' ? 'selected' : '' ?>>Milk</option>
      <option value="Flowers" <?= $category == 'Flowers' ? 'selected' : '' ?>>Flowers</option>
    </select>
    <button type="submit">Search</button>
  </form>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="supplier-card">
        <h3><?= htmlspecialchars($row['name']) ?></h3>
        <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($row['description']) ?></p>
        <p><strong>Mobile No:</strong> <?=htmlspecialchars($row['mobileno'])?></p>
        <a class="profile-link" href="supplier/dashboard.php?id=<?= $row['id'] ?>">View Profile</a>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No suppliers found matching your criteria.</p>
  <?php endif; ?>
</div>

</body>
</html>
