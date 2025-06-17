<?php
session_start();
include('connect.php');

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM admin_login WHERE Name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['Password'])) {
        // ✅ Success
        $_SESSION['admin'] = $username;
        header("file:///C:/Users/arvin/OneDrive/Desktop/Admin%20portal%20.html");
        exit();
    } else {
        echo "❌ Incorrect password.";
    }
} else {
    echo "❌ Admin not found.";
}

$stmt->close();
$conn->close();
?>
