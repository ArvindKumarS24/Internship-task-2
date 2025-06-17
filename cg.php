<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Only process form if it's submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Get POST data with validation
    // Get POST data with validation
// Get POST data with validation
$nov = trim($_POST['num-voters'] ?? '');  // Number of voters
$gn = trim($_POST['group-name'] ?? '');   // Group name
$cn = trim($_POST['candidate-name'] ?? ''); // Candidate name

// Basic validation
if (empty($nov)) {
    $errors[] = "Number of voters is required";
} elseif (!ctype_digit($nov)) {  // Changed from is_numeric to ctype_digit
    $errors[] = "Number of voters must be a whole number";
} elseif ($nov <= 0) {
    $errors[] = "Number of voters must be positive";
}
if (empty($gn)) {
    $errors[] = "Group name is required";
}
if (empty($cn)) {
    $errors[] = "Candidate name is required";
}
    if (empty($errors)) {
        // Prepare the insert query
        $stmt = $conn->prepare("INSERT INTO cg (nov, gn, cn) VALUES (?, ?, ?)");
        
        if ($stmt === false) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            // Bind parameters
            $bind_result = $stmt->bind_param("iss", $nov, $gn, $cn);
            
            if ($bind_result === false) {
                $errors[] = "Bind failed: " . $stmt->error;
            } else {
                // Execute the statement
                if ($stmt->execute()) {
                    $success = "✅ Group created successfully!";
                    // Clear form
                    $_POST['num-voters'] = '';
                    $_POST['group-name'] = '';
                    $_POST['candidate-name'] = '';
                } else {
                    $errors[] = "Execute failed: " . $stmt->error;
                }
            }
            
            $stmt->close();
        }
    }
}
?>

<!-- Rest of your HTML remains the same -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.6);
        }
        .video-background {
            position: fixed;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            transform: translate(-50%, -50%);
            filter: brightness(50%);
        }
        .container {
            width: 90%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgb(0, 0, 0);
            animation: fadeIn 1s ease-in-out;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 28px;
            text-transform: uppercase;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .form-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 10px rgba(52, 152, 219, 0.4);
            outline: none;
        }
        .form-group input[type="submit"] {
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .form-group input[type="submit"]:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        .form-group input[type="submit"]:active {
            background-color: #1c6391;
            transform: translateY(0);
        }
        .success-message, .error-message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
        }
        .success-message {
            color: #27ae60;
        }
        .error-message {
            color: #e74c3c;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Background Video -->
    <video class="video-background" autoplay loop muted>
        <source src="/video/AdobeStock_353586775_Video_4K_Preview.mov" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Form Container -->
    <div class="container">
        <h2>Create Group</h2>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="error-message">❌ <?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message">✅ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="num-voters">Number of Voters:</label>
                <input type="number" id="num-voters" name="num-voters" required 
                       value="<?php echo htmlspecialchars($_POST['num-voters'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="group-name">Group Name:</label>
                <input type="text" id="group-name" name="group-name" required
                       value="<?php echo htmlspecialchars($_POST['group-name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="candidate-name">Candidate Name:</label>
                <input type="text" id="candidate-name" name="candidate-name" required
                       value="<?php echo htmlspecialchars($_POST['candidate-name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="Create Group">
            </div>
        </form>
    </div>

</body>
</html>

<?php
// Example PHP close connection if you’re using DB logic above this part
if (isset($conn)) {
    $conn->close();
}
?>
