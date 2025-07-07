<?php
session_start();
include '../db.php';

// Ensure supplier is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get supplier id for this user
$stmt = $conn->prepare("SELECT id FROM suppliers WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $supplierId = $row['id'];
} else {
    die("Supplier profile not found for the logged-in user.");
}

$errors = [];
$success = "";

// Handle product addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $desc = trim($_POST['product_desc']);

    // Validate product name and description
    if (empty($name)) {
        $errors[] = "Product name is required.";
    }
    if (empty($desc)) {
        $errors[] = "Product description is required.";
    }

    // Handle file upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../uploads/products/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imagePath = 'uploads/products/' . $newFileName;
            } else {
                $errors[] = "There was an error moving the uploaded file.";
            }
        } else {
            $errors[] = "Upload failed. Allowed file types: " . implode(", ", $allowedfileExtensions);
        }
    } else {
        $errors[] = "Please upload an image file.";
    }

    // Insert product if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO products (supplier_id, name, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $supplierId, $name, $desc, $imagePath);

        if ($stmt->execute()) {
            $success = "Product added successfully!";
        } else {
            $errors[] = "Database error: Could not add product. " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch supplier's products
$stmt = $conn->prepare("SELECT * FROM products WHERE supplier_id = ?");
$stmt->bind_param("i", $supplierId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Products - Supplier Dashboard</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    h2 {
      color: #2a9d8f;
    }
    form {
      margin-bottom: 40px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }
    button {
      background: #2a9d8f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 1rem;
    }
    button:hover {
      background-color: #24756f;
    }
    .product-card {
      background: #ecfdf5;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      display: flex;
      align-items: center;
    }
    .product-card img {
      max-width: 120px;
      max-height: 120px;
      border-radius: 6px;
      margin-right: 15px;
      object-fit: cover;
    }
    .product-info {
      flex: 1;
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
      font-weight: bold;
    }
    nav a:hover {
      text-decoration: underline;
    }
    .error {
      color: #721c24;
      background-color: #f8d7da;
      border: 1px solid #f5c6cb;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }
    .success {
      color: #155724;
      background-color: #d4edda;
      border: 1px solid #c3e6cb;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<nav>
  <a href="../buyer/inbox.php">Messages</a>
  <a href="../logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Add New Product</h2>

  <?php if (!empty($errors)): ?>
    <div class="error">
      <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <label>Product Name</label>
    <input type="text" name="product_name" required>

    <label>Image File</label>
    <input type="file" name="product_image" accept="image/*" required>

    <label>Description</label>
    <textarea name="product_desc" rows="3" required></textarea>

    <button type="submit" name="add_product">Add Product</button>
  </form>

  <h2>Your Products</h2>

  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="product-card">
      <img src="../<?= htmlspecialchars($row['image']) ?>" alt="Product Image">
      <div class="product-info">
        <h4><?= htmlspecialchars($row['name']) ?></h4>
        <p><?= htmlspecialchars($row['description']) ?></p>
      </div>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
