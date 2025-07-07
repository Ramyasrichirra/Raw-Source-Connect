<?php
include 'db.php';

$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = trim($_POST['user_id']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $mobileno = trim($_POST['mobileno']);

    if (empty($userId) || !is_numeric($userId)) $errors[] = "Valid User ID is required.";
    if (empty($category)) $errors[] = "Category is required.";
    if (empty($address)) $errors[] = "Address is required.";
    if (empty($mobileno) || !preg_match('/^\+?\d{7,15}$/', $mobileno)) $errors[] = "Valid mobile number is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO suppliers (user_id, category, description, address, mobileno) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $category, $description, $address, $mobileno);
        if ($stmt->execute()) {
            $success = "Supplier profile added successfully.";
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Supplier Profile</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f2f5; padding: 40px; }
    .container { max-width: 600px; background: white; padding: 30px; margin: auto; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    h2 { color: #2a9d8f; }
    input, textarea { width: 100%; padding: 10px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc; }
    label { display: block; margin-top: 15px; font-weight: bold; }
    button { margin-top: 20px; background: #2a9d8f; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
    .error { color: red; margin-top: 10px; }
    .success { color: green; margin-top: 10px; }
  </style>
</head>
<body>

<div class="container">
  <h2>Add Supplier Profile</h2>

  <?php if (!empty($errors)): ?>
    <div class="error"><?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>User ID</label>
    <input type="number" name="user_id" required>

    <label>Category</label>
    <input type="text" name="category" placeholder="e.g., Organic Milk, Herbal Plants" required>

    <label>Description</label>
    <textarea name="description" rows="3" placeholder="Tell buyers about your products"></textarea>

    <label>Address</label>
    <input type="text" name="address" required>

    <label>Mobile Number</label>
    <input type="text" name="mobileno" placeholder="+1234567890" required>

    <button type="submit">Save Profile</button>
  </form>
</div>

</body>
</html>
