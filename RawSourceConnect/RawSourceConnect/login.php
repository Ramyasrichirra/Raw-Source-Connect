<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #74ebd5, #ACB6E5);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: #fff;
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #333;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-weight: 600;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      transition: border 0.3s ease;
    }

    input:focus, select:focus {
      border-color: #6c63ff;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #6c63ff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #574fd6;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <form method="POST" action="auth/login_process.php">
      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required>

      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required>

      <label for="role">Login As:</label>
      <select name="role" id="role" required>
        <option value="">Select Role</option>
        <option value="buyer">buyer</option>
        <option value="supplier">supplier</option>
      </select>

      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
