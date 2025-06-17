<?php
session_start();
include 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login11.php");
    exit();
}

// Define the variable BEFORE using it
$currentUsername = $_SESSION['username'];

// Fetch user data (email) from database
$stmt = $conn->prepare("SELECT email FROM signup11 WHERE Name = ?");
$stmt->bind_param("s", $currentUsername);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Secure Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .dashboard-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .user-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="dashboard-card">
            <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($currentUsername); ?> 🎉</h2>
            
            <div class="user-info">
                <h4 class="mb-3">Your Account Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($currentUsername); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Email:</strong> <?php echo isset($userData['email']) ? htmlspecialchars($userData['email']) : 'N/A'; ?></p>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="login11.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
