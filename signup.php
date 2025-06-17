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

// Only process form if it's submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data with validation
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone) || !preg_match('/^[0-9]{10,15}$/', $phone)) $errors[] = "Valid phone number is required";
    if (empty($gender)) $errors[] = "Gender is required";
    if (empty($dob)) $errors[] = "Date of Birth is required";
    if (empty($password) || strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    
    // Validate date format
    if (!empty($dob)) {
        $date = DateTime::createFromFormat('Y-m-d', $dob);
        if (!$date || $date->format('Y-m-d') !== $dob) {
            $errors[] = "Invalid date format (YYYY-MM-DD required)";
        }
    }
    
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare the insert query
        $stmt = $conn->prepare("INSERT INTO signup (name, email, phone, gender, dob, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $gender, $dob, $hashed_password);
        
        if ($stmt->execute()) {
            $success = "✅ Sign-up successful!";
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Voting Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('img/1738230506338.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0);
            z-index: 0;
        }
        
        .signup-container {
            background: rgb(255, 255, 255);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0);
            width: 90%;
            max-width: 400px;
            max-height: 600px; /* Reduced height */
            z-index: 1;
            position: relative;
            animation: fadeIn 0.5s ease-out;
            backdrop-filter: blur(5px);
            border: 1px solid rgb(12, 206, 5);
            overflow-y: auto; /* Add scroll if content overflows */
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        h2 {
            color:rgb(13, 185, 28);
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color:rgb(20, 165, 14);
            font-size: 14px;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: all 0.3s;
            background-color: rgb(255, 255, 255);
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0);
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn:hover {
            background-color: #2ecc71;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .error {
            color: #e74c3c;
            margin-bottom: 20px;
            padding: 12px;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 4px;
            border-left: 4px solid #e74c3c;
            font-size: 14px;
        }
        
        .success {
            color: #27ae60;
            margin-bottom: 20px;
            padding: 12px;
            background-color: rgba(39, 174, 96, 0.1);
            border-radius: 4px;
            border-left: 4px solid #27ae60;
            font-size: 14px;
        }
        
        @media (max-width: 480px) {
            .signup-container {
                padding: 20px;
                width: 95%;
            }
            
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create Your Account</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="signup.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required 
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                       pattern="[0-9]{10,15}" title="10-15 digit phone number">
            </div>
            
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" required 
                       value="<?php echo htmlspecialchars($_POST['dob'] ?? ''); ?>"
                       max="<?php echo date('Y-m-d'); ?>">
            </div>
            
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo (($_POST['gender'] ?? '') === 'Male' ? 'selected' : ''); ?>>Male</option>
                    <option value="Female" <?php echo (($_POST['gender'] ?? '') === 'Female' ? 'selected' : ''); ?>>Female</option>
                    <option value="Other" <?php echo (($_POST['gender'] ?? '') === 'Other' ? 'selected' : ''); ?>>Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password">Password (min 6 characters)</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            
            <button type="submit" class="submit-btn">Register Now</button>
        </form>
    </div>
    
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                document.getElementById('confirm-password').focus();
            }
        });
    </script>
</body>
</html>