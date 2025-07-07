<?php
session_start();
include '../db.php';

// Validate and get supplier ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid supplier ID.");
}
$id = (int) $_GET['id'];

// Get supplier information
$supplierQuery = $conn->prepare("
    SELECT u.name, u.email, s.address, s.category ,s.mobileno
    FROM users u 
    JOIN suppliers s ON u.id = s.id 
    WHERE u.id = ?
");
$supplierQuery->bind_param("i", $id);
$supplierQuery->execute();
$supplierResult = $supplierQuery->get_result();
$supplier = $supplierResult->fetch_assoc();
if (!$supplier) {
    die("Supplier not found or invalid supplier ID.");
}

// Get products posted by this supplier
$productQuery = $conn->prepare("SELECT * FROM products WHERE supplier_id = ?");
$productQuery->bind_param("i", $id);
$productQuery->execute();
$productResult = $productQuery->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($supplier['name']) ?> - Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f9f9f9;
        }

        /* Hamburger button always visible */
        .hamburger {
            position: fixed;
            top: 15px;
            left: 15px;
            font-size: 28px;
            cursor: pointer;
            color: white;
            background: #2a9d8f;
            border: none;
            padding: 10px 14px;
            border-radius: 6px;
            z-index: 1001;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -260px;
            width: 250px;
            height: 100%;
            background-color: #2a9d8f;
            color: white;
            transition: left 0.3s ease-in-out;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar p {
            margin: 10px 0;
        }

        /* Main content */
        .content {
            margin-left: 0;
            padding: 60px 30px 30px 30px;
            transition: margin-left 0.3s ease-in-out;
        }

        .content.shift {
            margin-left: 250px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        .product-card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .product-card:hover {
            transform: translateY(-3px);
        }

        button.message-btn {
            background: #ffffff;
            color: #2a9d8f;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        button.message-btn:hover {
            background: #e0f4f2;
        }
    </style>
</head>
<body>

<!-- Hamburger Menu -->
<button class="hamburger" onclick="toggleSidebar()">☰</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <h2><?= htmlspecialchars($supplier['name']) ?></h2>
    <p><strong>Email:</strong> <?= htmlspecialchars($supplier['email']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($supplier['address']) ?></p>
    <p><strong>Category:</strong> <?= htmlspecialchars($supplier['category']) ?></p>
    <p><strong>Mobile No:</strong> <?= htmlspecialchars($supplier['mobileno']) ?></p>

    <button class="message-btn" onclick="window.location.href='messages.php?to=<?= $_GET['id'] ?>'">Message Supplier</button>
</div>

<!-- Main Content -->
<div class="content" id="mainContent">
    <h2>Products by <?= htmlspecialchars($supplier['name']) ?></h2>

    <?php if ($productResult->num_rows > 0): ?>
        <?php while ($product = $productResult->fetch_assoc()): ?>
            <div class="product-card">
                
                 <img src="../<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['description']) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No products found for this supplier.</p>
    <?php endif; ?>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");

    sidebar.classList.toggle("active");
    mainContent.classList.toggle("shift");
}
</script>

</body>
</html>
