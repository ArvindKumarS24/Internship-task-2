<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "1234";
$database = "vsm";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$errors = [];
$success = "";
$positions = ['President', 'Vice President', 'NCC Head', 'Sports Secretary', 'Cultural Secretary', 'Class CR', 'Choir Head', 'KCDC Head'];

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle new position addition
    if (isset($_POST['new_position'])) {
        $newPosition = trim($_POST['new_position']);
        
        if (empty($newPosition)) {
            $errors[] = "Position name cannot be empty";
        } else {
            // Add to our positions array (in a real app, you'd store this in database)
            if (!in_array($newPosition, $positions)) {
                $positions[] = $newPosition;
                $success = "✅ New position '$newPosition' added successfully!";
            } else {
                $errors[] = "Position '$newPosition' already exists";
            }
        }
    }
    
    // Handle vote submission
    if (isset($_POST['name']) && isset($_POST['gp'])) {
        $name = trim($_POST['name'] ?? '');
        $gp = trim($_POST['gp'] ?? '');
        
        // Validation
        if (empty($name)) $errors[] = "Voter name is required";
        if (empty($gp)) $errors[] = "Position selection is required";
        
        // If no errors, insert into database
        if (empty($errors)) {
            // Check if voter already exists
            $check_stmt = $conn->prepare("SELECT name FROM vote WHERE name = ?");
            if ($check_stmt === false) {
                $errors[] = "Database error: " . $conn->error;
            } else {
                $check_stmt->bind_param("s", $name);
                $check_stmt->execute();
                $check_stmt->store_result();
                
                if ($check_stmt->num_rows > 0) {
                    $errors[] = "This voter has already voted";
                } else {
                    // Prepare the insert statement with timestamp
                    $stmt = $conn->prepare("INSERT INTO vote (name, gp, vt) VALUES (?, ?, NOW())");
                    if ($stmt === false) {
                        $errors[] = "Database error: " . $conn->error;
                    } else {
                        $stmt->bind_param("ss", $name, $gp);
                        
                        if ($stmt->execute()) {
                            $success = "✅ Vote submitted successfully at " . date('Y-m-d H:i:s');
                        } else {
                            $errors[] = "Error submitting vote: " . $stmt->error;
                        }
                        
                        $stmt->close();
                    }
                }
                $check_stmt->close();
            }
        }
    }
}

// Fetch all votes to display
$votes = [];
$query = "SELECT name, gp, vt FROM vote ORDER BY vt DESC";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $votes[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Management</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgb(66, 226, 63);
        }
        h1, h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        input[type="text"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus, select:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 3px rgba(62, 143, 235, 0);
        }
        button {
            width: 100%;
            padding: 14px;
            background-color:rgb(38, 210, 47);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color:rgb(12, 189, 12);
        }
        .btn-add {
            background-color:rgb(45, 186, 76);
        }
        .btn-add:hover {
            background-color:rgb(17, 141, 44);
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .vote {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .vote th, .vote td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .vote th {
            background-color: #f2f2f2;
            font-weight: 600;
        }
        .vote tr:hover {
            background-color: #f5f5f5;
        }
        .timestamp {
            font-family: monospace;
            color: #666;
        }
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            background: #f1f1f1;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        .tab.active {
            background:rgb(15, 202, 18);
            color: white;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vote Your Candidate</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="openTab('vote')">Cast Vote</div>
            <div class="tab" onclick="openTab('manage')">Add Positions</div>
        </div>
        
        <div id="vote" class="tab-content active">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Candidate Name</label>
                    <input type="text" id="name" name="name" required placeholder="Enter Candidate Name">
                </div>
                
                <div class="form-group">
                    <label for="gp">Select Position to Vote For</label>
                    <select id="gp" name="gp" required>
                        <option value="">-- Choose Position --</option>
                        <?php foreach ($positions as $position): ?>
                            <option value="<?php echo htmlspecialchars($position); ?>"><?php echo htmlspecialchars($position); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit">Submit Your Vote</button>
            </form>
        </div>
        
        <div id="manage" class="tab-content">
            <h2>Add New Position</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="new_position">New Position Name</label>
                    <input type="text" id="new_position" name="new_position" required placeholder="Enter new position name">
                </div>
                <button type="submit" class="btn-add">Add Position</button>
            </form>
            
            <h2>Current Positions</h2>
            <ul>
                <?php foreach ($positions as $position): ?>
                    <li><?php echo htmlspecialchars($position); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if (!empty($votes)): ?>
            <h2>Voting Records</h2>
            <table class="vote">
                <thead>
                    <tr>
                        <th>Voter name</th>
                        <th>Position</th>
                        <th>Vote Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($votes as $vote): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vote['name']); ?></td>
                            <td><?php echo htmlspecialchars($vote['gp']); ?></td>
                            <td class="timestamp"><?php echo htmlspecialchars($vote['vt']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function openTab(tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tabs
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Show the current tab and mark button as active
            document.getElementById(tabName).classList.add('active');
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>