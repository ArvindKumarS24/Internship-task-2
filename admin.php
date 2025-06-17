<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
include('connect.php');

// Initialize error variable
$error = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate inputs
    if (empty($username) || empty($password)) {
        $error = "❌ Both username and password are required!";
    } else {
        // Prepare the SQL statement to fetch the admin's credentials
        $sql = "SELECT * FROM `admin login` WHERE `Admin Name` = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            
            if (!$stmt->execute()) {
                $error = "❌ Database error: " . $stmt->error;
            } else {
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $row = $result->fetch_assoc();
                    
                    // Verify the password
                    if (password_verify($password, $row['Password'])) {
                        // Start session and store admin data
                        session_start();
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_username'] = $row['Admin Name'];
                        
                        // Redirect to admin dashboard
                        header('Location: admin_dashboard.php');
                        exit();
                    } else {
                        $error = "❌ Incorrect password!";
                    }
                } else {
                    $error = "❌ Admin not found!";
                }
            }
            
            $stmt->close();
        } else {
            $error = "❌ Database error: " . $conn->error;
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        #video-background {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            object-fit: cover;
            opacity: 0.7;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.3);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: #333;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        input[type="text"], 
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
            background-color: rgba(255,255,255,0.9);
        }

        input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #5a6fd1;
        }

        .error-message {
            color: #e74c3c;
            margin: 15px 0;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <video autoplay muted loop id="video-background">
        <source src="video/AdobeStock_353586775_Video_4K_Preview.mov" type="video/mp4">
        Your browser does not support HTML5 video.
    </video>
    
    <div class="login-container">
        <h2>Admin Login</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="process_admin.php" method="POST" autocomplete="off">
            <div class="input-group">
                <input type="text" name="username" placeholder="Admin Username" required autofocus value="">
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Admin Password" required value="">
            </div>
            <button type="file:///C:/Users/arvin/OneDrive/Desktop/Admin%20portal%20.html">Login</button>
        </form>
    </div>
</body>
</html>