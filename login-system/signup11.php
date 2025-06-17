<?php
session_start();
include 'conn.php';

$message = ''; // Initialize empty message

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $password !== $confirm_password) {
        $message = "❌ Please fill all fields correctly and ensure passwords match.";
    } else {
        // ✅ Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO signup11 (name, email, password) VALUES (?, ?, ?)");

        if (!$stmt) {
            $message = "❌ Database Error: " . $conn->error;
        } else {
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "✅ Registered successfully. <a href='login11.php'>Login here</a>";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up | Secure Portal</title>
  <link rel="stylesheet" href="style.css"/>
  <script>
    function togglePassword(id, iconId) {
      const field = document.getElementById(id);
      const icon = document.getElementById(iconId);
      if (field.type === "password") {
        field.type = "text";
        icon.textContent = "🙈";
      } else {
        field.type = "password";
        icon.textContent = "👁️";
      }
    }

    function validateForm(event) {
      const username = document.forms["signupForm"]["username"].value.trim();
      const email = document.forms["signupForm"]["email"].value.trim();
      const password = document.forms["signupForm"]["password"].value;
      const confirm = document.forms["signupForm"]["confirm_password"].value;

      const usernameRegex = /^[a-zA-Z0-9_]{4,}$/;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

      if (!usernameRegex.test(username)) {
        alert("Username must be at least 4 characters and contain no spaces.");
        event.preventDefault();
        return false;
      }

      if (!emailRegex.test(email)) {
        alert("Please enter a valid email address.");
        event.preventDefault();
        return false;
      }

      if (!passwordRegex.test(password)) {
        alert("Password must be at least 8 characters, including a number, uppercase and lowercase letter.");
        event.preventDefault();
        return false;
      }

      if (password !== confirm) {
        alert("Passwords do not match.");
        event.preventDefault();
        return false;
      }

      return true;
    }
  </script>
</head>
<body>
  <?php if (!empty($message)): ?>
  <div class="message-box" style="
      background: <?= str_contains($message, '✅') ? '#e8f5e9' : '#ffebee' ?>;
      color: <?= str_contains($message, '✅') ? '#2e7d32' : '#c62828' ?>;
      padding: 12px 15px;
      border-radius: 8px;
      margin: 20px auto;
      width: 90%;
      max-width: 500px;
      border-left: 5px solid <?= str_contains($message, '✅') ? '#2e7d32' : '#c62828' ?>;
      font-family: sans-serif;
    ">
    <?= $message ?>
  </div>
  <?php endif; ?>

  <div class="login-container">
    <h2>Create Account</h2>
    <p class="subtitle">Use a strong password for better protection</p>
    <form name="signupForm" action="signup11.php" method="post" class="login-form" onsubmit="return validateForm(event)" novalidate>

      <div class="input-group">
        <span class="icon">👤</span>
        <input type="text" name="username" placeholder="Username" required />
      </div>

      <div class="input-group">
        <span class="icon">📧</span>
        <input type="email" name="email" placeholder="Email" required />
      </div>

      <div class="input-group">
        <span class="icon">🔒</span>
        <input type="password" name="password" id="password" placeholder="Password" required />
        <span class="toggle-password" onclick="togglePassword('password', 'pass-icon')" id="pass-icon">👁️</span>
      </div>

      <div class="input-group">
        <span class="icon">✅</span>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required />
        <span class="toggle-password" onclick="togglePassword('confirm_password', 'confirm-icon')" id="confirm-icon">👁️</span>
      </div>

      <button type="submit">Sign Up</button>
      <p>Already have an account? <a href="login11.php">Login</a></p>
    </form>
  </div>
</body>
</html>
