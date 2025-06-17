<?php
$host = "localhost";
$user = "root";
$password = "1234"; // default XAMPP MySQL password is blank
$dbname = "users";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully!";
}
?>
