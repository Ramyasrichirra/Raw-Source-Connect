<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Sign Up - RawSource Connect</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f4f3;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 400px;
      margin: 80px auto;
      background: white;
      padding: 30px 40px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    h2 {
      text-align: center;
      color: #2a9d8f;
    }

    label {
      display: block;
      margin-top: 15px;
      color: #333;
      font-weight: 500;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      width: 100%;
      background: #2a9d8f;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      margin-top: 25px;
      cursor: pointer;
    }

    button:hover {
      background: #23867b;
    }

    .login-link {
      margin-top: 20px;
      text-align: center;
      font-size: 14px;
    }

    .login-link a {
      color: #2a9d8f;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>Create Account</h2>
  <form action="auth/signup_process.php" method="POST">
    <label for="name">Full Name</label>
    <input type="text" name="name" required>

    <label for="email">Email Address</label>
    <input type="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" name="password" required>

    <label for="role">Register As</label>
    <select name="role" required>
      <option value="">-- Select Role --</option>
      <option value="buyer">Buyer</option>
      <option value="supplier">Supplier</option>
    </select>

    <button type="submit">Sign Up</button>
  </form>

  <div class="login-link">
    Already have an account? <a href="login.php">Login here</a>
  </div>
</div>

</body>
</html>
