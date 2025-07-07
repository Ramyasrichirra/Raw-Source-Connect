<?php
include 'db.php';
$id = $_GET['id'];

$sql = "SELECT s.*, u.name FROM suppliers s JOIN users u ON s.user_id = u.id WHERE s.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();

echo "<h2>{$supplier['name']}</h2>";
echo "<p>{$supplier['description']}</p>";
echo "<p>Location: {$supplier['address']}</p>";
echo "<p>Rating: {$supplier['average_rating']} / 5</p>";
?>
<form action="buyer/message.php" method="POST">
  <input type="hidden" name="to_supplier_id" value="<?= $supplier['id'] ?>">
  <label>Subject:</label><br>
  <input type="text" name="subject"><br>
  <label>Message:</label><br>
  <textarea name="message"></textarea><br>
  <button type="submit">Send Message</button>
</form>
