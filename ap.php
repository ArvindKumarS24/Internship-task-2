<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "vsm";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message
$message = '';
$messageClass = 'success';

// Process Delete Record form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_record'])) {
    $name = trim($_POST['name'] ?? '');
    $gname = trim($_POST['gname'] ?? '');

    if (!empty($name) && !empty($gname)) {
        $sql = "DELETE FROM ap WHERE name = ? AND gname = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            $message = "Database error: " . $conn->error;
            $messageClass = 'error';
        } else {
            $stmt->bind_param("ss", $name, $gname);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "Record deleted successfully!";
                    $messageClass = 'success';
                } else {
                    $message = "No matching record found to delete.";
                    $messageClass = 'error';
                }
            } else {
                $message = "Error deleting record: " . $stmt->error;
                $messageClass = 'error';
            }
            $stmt->close();
        }
    } else {
        $message = "Both name and group are required!";
        $messageClass = 'error';
    }
}

// Fetch all records
$records = [];
$result = $conn->query("SELECT name, gname FROM ap ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $result->free();
} else {
    $message = "Error fetching records: " . $conn->error;
    $messageClass = 'error';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Voting Management</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            padding: 30px;
        }
        .container {
            max-width: 850px;
            margin: auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        h1, h2 {
            text-align: center;
            color: #2c3e50;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            color: #34495e;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            border-color: #3498db;
        }
        button {
            background-color: #e74c3c;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
        }
        button:hover {
            background-color: #c0392b;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: bold;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #f1f1f1;
            color: #2c3e50;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel - Voting Management System</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <h2>Delete Voter Record</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Voter Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="gname">Group Name:</label>
                <input type="text" id="gname" name="gname" required>
            </div>
            <button type="submit" name="delete_record">Delete Record</button>
        </form>

        <?php if (!empty($records)): ?>
            <h2>Current Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Group</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?= htmlspecialchars($record['name']) ?></td>
                            <td><?= htmlspecialchars($record['gname']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="footer">
            &copy; <?= date('Y') ?> Voting Management System | Admin Panel
        </div>
    </div>
</body>
</html>