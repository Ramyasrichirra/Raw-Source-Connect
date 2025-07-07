<?php
session_start();
include '../db.php';

// Ensure supplier is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'supplier') {
    header("Location: ../login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Get the supplier's ID
$stmt = $conn->prepare("SELECT id FROM suppliers WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Supplier profile not found.");
}
$supplierId = $result->fetch_assoc()['id'];

// Handle reply
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reply_submit'])) {
    $msgId = intval($_POST['message_id']);
    $replyText = trim($_POST['reply_text']);
    $replyDate = date('Y-m-d H:i:s');

    if (!empty($replyText)) {
        $stmt = $conn->prepare("UPDATE messages SET reply = ?, replydate = ? WHERE id = ? AND to_supplier_id = ?");
        $stmt->bind_param("ssii", $replyText, $replyDate, $msgId, $supplierId);
        $stmt->execute();
    }
}

// Fetch messages sent to this supplier
$stmt = $conn->prepare("
    SELECT m.*, COALESCE(u.name, 'Unknown Buyer') AS buyer_name
    FROM messages m
    LEFT JOIN users u ON m.from_user_id = u.id
    WHERE m.to_supplier_id = ?
    ORDER BY m.timestamp DESC
");
$stmt->bind_param("i", $supplierId);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Buyer Messages</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; padding: 20px; }
    .msg-box {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 15px;
      margin-bottom: 20px;
    }
    .msg-box h3 { margin: 0; color: #2a9d8f; }
    .msg-box small { color: gray; }
    .msg-box p { margin: 10px 0; }
    textarea {
      width: 100%; padding: 10px; border-radius: 5px;
      border: 1px solid #ccc; resize: vertical;
    }
    button {
      background: #2a9d8f; color: white; padding: 8px 16px;
      border: none; border-radius: 6px; cursor: pointer;
      margin-top: 8px;
    }
    button:hover { background: #24756f; }
    .reply { background: #e8f7f3; padding: 10px; border-radius: 5px; margin-top: 10px; }
  </style>
</head>
<body>

<h2>Messages from Buyers</h2>

<?php if ($messages->num_rows === 0): ?>
  <p>No messages yet.</p>
<?php else: ?>
  <?php while ($msg = $messages->fetch_assoc()): ?>
    <div class="msg-box">
      <h3><?= htmlspecialchars($msg['subject']) ?></h3>
      <small>From: <?= htmlspecialchars($msg['buyer_name']) ?> | <?= htmlspecialchars($msg['timestamp']) ?></small>
      <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>

      <?php if (!empty($msg['reply'])): ?>
        <div class="reply">
          <strong>Your Reply (<?= $msg['replydate'] ?>):</strong>
          <p><?= nl2br(htmlspecialchars($msg['reply'])) ?></p>
        </div>
      <?php else: ?>
        <form method="POST">
          <textarea name="reply_text" rows="3" placeholder="Write your reply..." required></textarea>
          <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
          <button type="submit" name="reply_submit">Send Reply</button>
        </form>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

</body>
</html>
