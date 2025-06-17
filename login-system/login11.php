<?php
session_start();
include 'conn.php';

$username = $_POST['username_email'] ?? '';
$password = $_POST['password'] ?? '';

if ($username && $password) {
    $stmt = $conn->prepare("SELECT * FROM signup11 WHERE name = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $row['name']; // Use actual username from DB
            header("Location: dash.php");
            exit();
        } else {
            echo "<script>alert('❌ Incorrect password.'); window.location='login11.php';</script>";
        }
    } else {
        echo "<script>alert('❌ Username or email not found.'); window.location='login11.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Secure Portal</title>
  <link rel="stylesheet" href="style.css"/>

  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* Body Background */
    body {
      background: linear-gradient(-45deg, #83a4d4, #b6fbff, #c3cfe2, #e0c3fc);
      background-size: 400% 400%;
      animation: gradientShift 10s ease infinite;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    @keyframes gradientShift {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }

    .login-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 400px;
      text-align: center;
      transition: 0.3s ease-in-out;
    }

    .login-container:hover {
      transform: translateY(-2px);
    }

    .login-container h2 {
      margin-bottom: 10px;
      color: #333;
    }

    .subtitle {
      margin-bottom: 25px;
      font-size: 14px;
      color: #666;
    }

    .login-form .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .login-form .icon {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      font-size: 16px;
    }

    .login-form input {
      width: 100%;
      padding: 12px 40px 12px 35px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 15px;
      transition: border-color 0.3s;
    }

    .login-form input:focus {
      border-color: #007bff;
      outline: none;
    }

    .toggle-password {
      position: absolute;
      right: 10px;
      top: 50%;
      font-size: 18px;
      transform: translateY(-50%);
      cursor: pointer;
    }

    .login-form button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      border: none;
      border-radius: 10px;
      color: white;
      font-size: 16px;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.3s;
    }

    .login-form button:hover {
      background-color: #0056b3;
    }

    .signup-link {
      margin-top: 15px;
      font-size: 14px;
      color: #444;
    }

    .signup-link a {
      color: #007bff;
      text-decoration: none;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 500px) {
      .login-container {
        padding: 25px 20px;
      }

      .login-form input {
        font-size: 14px;
      }
    }
  </style>

  <script>
    function togglePassword() {
      const password = document.getElementById("password");
      const icon = document.getElementById("toggle-icon");
      if (password.type === "password") {
        password.type = "text";
        icon.textContent = "🙈";
      } else {
        password.type = "password";
        icon.textContent = "👁️";
      }
    }

    function validateLogin(event) {
      const usernameEmail = document.forms["loginForm"]["username_email"].value.trim();
      const password = document.forms["loginForm"]["password"].value;

      if (usernameEmail.length < 4 || usernameEmail.length > 100) {
        alert("Enter a valid username or email (4 to 100 characters).");
        event.preventDefault();
        return false;
      }

      if (password.length < 8) {
        alert("Password must be at least 8 characters.");
        event.preventDefault();
        return false;
      }

      return true;
    }
  </script>
</head>
<body>
  <div class="login-container">
    <h2>Welcome Back 👋</h2>
    <p class="subtitle">Please login to your account</p>
    <form name="loginForm" action="login11.php" method="post" class="login-form" onsubmit="return validateLogin(event)" autocomplete="off" novalidate>

      <div class="input-group">
        <span class="icon">📧</span>
        <input type="text" name="username_email" placeholder="Username or Email" minlength="4" maxlength="100" required />
      </div>

      <div class="input-group">
        <span class="icon">🔒</span>
        <input type="password" name="password" id="password" placeholder="Password" minlength="8" maxlength="100" required autocomplete="new-password" />
        <span class="toggle-password" onclick="togglePassword()" id="toggle-icon">👁️</span>
      </div>

      <button type="submit">Login</button>

      <p class="signup-link">Don't have an account? <a href="signup11.php">Sign up</a></p>
    </form>
  </div>
</body>
</html>
