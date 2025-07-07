<?php
session_start();
include '../db.php';

// Check if buyer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$buyerId = $_SESSION['user_id'];

$success = $error = "";

// Fetch suppliers with their user names for dropdown
// Fetch suppliers with their user names for dropdown


$suppliersStmt = $conn->prepare("
    SELECT id, name
    FROM users
    WHERE role = 'supplier'
");
$suppliersStmt->execute();
$suppliers = $suppliersStmt->get_result();



// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $toSupplierId = (int)$_POST['to_supplier_id'];
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($toSupplierId && $subject !== '' && $message !== '') {
        $stmt = $conn->prepare("
            INSERT INTO messages (from_user_id, to_supplier_id, subject, message, timestamp)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iiss", $buyerId, $toSupplierId, $subject, $message);

        if ($stmt->execute()) {
            $success = "Message sent successfully.";
        } else {
            $error = "Failed to send message: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Send Message to Supplier</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f8f9fa;
      margin: 0; padding: 20px;
    }
    .container {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #2a9d8f;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    select, input[type=text], textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
      resize: vertical;
      font-size: 1rem;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #2a9d8f;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 1rem;
    }
    button:hover {
      background-color: #24756f;
    }
    .message-status {
      margin-top: 20px;
      padding: 10px;
      border-radius: 6px;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    nav {
      text-align: right;
      margin-bottom: 20px;
    }
    nav a {
      color: #2a9d8f;
      text-decoration: none;
      margin-left: 15px;
      font-weight: bold;
    }
    nav a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<nav>
  <a href="../buyer/dashboard.php">Dashboard</a>
  <a href="../logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Send Message to Supplier</h2>

  <?php if ($success): ?>
    <div class="message-status success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="message-status error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <label for="to_supplier_id">Select Supplier:</label>
    <select name="to_supplier_id" id="to_supplier_id" required>
      <option value="">-- Select Supplier --</option>
      <?php while ($supplier = $suppliers->fetch_assoc()): ?>
        <option value="<?= (int)$supplier['id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
      <?php endwhile; ?>
    </select>

    <label for="subject">Subject:</label>
    <input type="text" id="subject" name="subject" required maxlength="255">

    <label for="message">Message:</label>
    <textarea id="message" name="message" rows="6" required></textarea>

    <button type="submit" name="send_message">Send Message</button>
  </form>
</div>

</body>
</html>
