<?php
$servername = "localhost";
$username = "root";
$password = "1234"; // Replace this
$database = "vsm";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$dob = $_POST['dob'] ?? '';
$gender = $_POST['gender'] ?? '';
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm-password'] ?? '';

// Hash the password (optional, but recommended)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO signup (name, email, phone, dob, gender, password)
        VALUES ('$name', '$email', '$phone', '$dob', '$gender', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "✅ Sign-up successful!";
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>
<form id="signup-form" action="signup_process.php" method="POST">

  <input type="text" name="name" placeholder="Name" required />
  <input type="email" name="email" placeholder="Email" required />
  <input type="text" name="phone" placeholder="Phone" required />
  <input type="date" name="dob" placeholder="Date of Birth" required />
  <select name="gender" required>
    <option value="">Select Gender</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <option value="Other">Other</option>
  </select>
  <input type="password" name="password" placeholder="Password" required />
  <button type="submit">Sign Up</button>
</form>
