<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>RawSource Connect</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header>
    <h1>RawSource Connect</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="browse.php">Browse Suppliers</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="signup.php">Sign Up</a>
      <?php endif; ?>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h2>Connecting you with sustainable raw material suppliers</h2>
      <p>Explore eco-friendly suppliers for plants, organic milk, flowers, and more.</p>
      <a href="browse.php" class="cta">Browse Suppliers</a>
    </section>

    <section class="features">
      <h3>Why RawSource Connect?</h3>
      <ul>
        <li>🌱 Eco-friendly suppliers at your fingertips</li>
        <li>📩 Direct messaging with suppliers</li>
        <li>⭐ Honest ratings and reviews from buyers</li>
      </ul>
    </section>
  </main>

  <footer>
    <p>&copy; <?= date("Y") ?> RawSource Connect. All rights reserved.</p>
  </footer>
</body>
</html>
