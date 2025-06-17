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
$formData = [
    'name' => '',
    'gender' => '',
    'dob' => '',
    'email' => '',
    'mobile' => '',
    'aadhar' => '',
    'pan' => '',
    'city' => ''
];

// Only process form if it's submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data with validation
    $formData = [
        'name' => trim($_POST['name'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'dob' => trim($_POST['dob'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'mobile' => trim($_POST['mobile'] ?? ''),
        'aadhar' => trim($_POST['aadhar'] ?? ''),
        'pan' => trim($_POST['pan'] ?? ''),
        'city' => trim($_POST['city'] ?? '')
    ];
    
    // Basic validation
    if (empty($formData['name'])) $errors[] = "Name is required";
    if (empty($formData['gender'])) $errors[] = "Gender is required";
    if (empty($formData['dob'])) $errors[] = "Date of Birth is required";
    if (empty($formData['email']) || !filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($formData['mobile']) || !preg_match('/^[0-9]{10}$/', $formData['mobile'])) $errors[] = "Valid 10-digit mobile number is required";
    if (empty($formData['aadhar']) || !preg_match('/^[0-9]{12}$/', $formData['aadhar'])) $errors[] = "Valid 12-digit Aadhar number is required";
    if (empty($formData['pan']) || !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $formData['pan'])) $errors[] = "Valid PAN number is required (format: ABCDE1234F)";
    if (empty($formData['city'])) $errors[] = "City is required";
    
    // Validate date format
    if (!empty($formData['dob'])) {
        $date = DateTime::createFromFormat('Y-m-d', $formData['dob']);
        if (!$date || $date->format('Y-m-d') !== $formData['dob']) {
            $errors[] = "Invalid date format (YYYY-MM-DD required)";
        }
    }
    
    if (empty($errors)) {
        // Prepare the insert query
        $stmt = $conn->prepare("INSERT INTO candidates (name, gender, dob, email, mobile, aadhar, pan, city) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("ssssssss", 
                $formData['name'],
                $formData['gender'],
                $formData['dob'],
                $formData['email'],
                $formData['mobile'],
                $formData['aadhar'],
                $formData['pan'],
                $formData['city']
            );
            
            if ($stmt->execute()) {
                $success = "✅ Candidate registered successfully!";
                // Clear form
                $formData = array_fill_keys(array_keys($formData), '');
            } else {
                $errors[] = "Execute failed: " . $stmt->error;
            }
            
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        #video-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        #video-background {
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            object-fit: cover;
            opacity: 0.8;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .container {
            position: relative;
            z-index: 1;
        }
        
        .registration-card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgb(0, 0, 0);
            overflow: hidden;
            background-color: rgb(255, 255, 255);
            backdrop-filter: blur(3px);
            border: 1px solid rgba(240, 229, 229, 0.45);
        }
        
        .card-header {
            background-color: rgba(13, 110, 253, 0.9);
            color: white;
            font-weight: 600;
            padding: 1.2rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .btn-submit {
            background-color: #0d6efd;
            border: none;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .gender-options {
            display: flex;
            gap: 15px;
            margin-top: 5px;
        }
        
        .gender-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .input-hint {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .alert {
            margin-bottom: 20px;
        }
        
        input, select, textarea {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border: 1px solid #ced4da !important;
            transition: all 0.3s ease;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            background-color: white !important;
        }
        
        @media (max-width: 768px) {
            .registration-card {
                margin: 20px;
            }
            #video-background {
                opacity: 0.7;
            }
        }
    </style>
</head>
<body>
   
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card registration-card">
                    <div class="card-header text-center">
                        <h3>Candidate Registration</h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label required-field">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($formData['name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required-field">Gender</label>
                                <div class="gender-options">
                                    <div class="gender-option">
                                        <input type="radio" id="male" name="gender" value="Male" 
                                               <?= $formData['gender'] === 'Male' ? 'checked' : '' ?> required>
                                        <label for="male">Male</label>
                                    </div>
                                    <div class="gender-option">
                                        <input type="radio" id="female" name="gender" value="Female" 
                                               <?= $formData['gender'] === 'Female' ? 'checked' : '' ?>>
                                        <label for="female">Female</label>
                                    </div>
                                    <div class="gender-option">
                                        <input type="radio" id="other" name="gender" value="Other" 
                                               <?= $formData['gender'] === 'Other' ? 'checked' : '' ?>>
                                        <label for="other">Other</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="dob" class="form-label required-field">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" 
                                       value="<?= htmlspecialchars($formData['dob']) ?>" required>
                                <div class="input-hint">Format: YYYY-MM-DD</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label required-field">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($formData['email']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="mobile" class="form-label required-field">Mobile Number</label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" 
                                       value="<?= htmlspecialchars($formData['mobile']) ?>" 
                                       pattern="[0-9]{10}" title="10-digit mobile number" required>
                                <div class="input-hint">10 digits only (no spaces or dashes)</div>
                            </div>

                            <div class="mb-3">
                                <label for="aadhar" class="form-label required-field">Aadhar Number</label>
                                <input type="text" class="form-control" id="aadhar" name="aadhar" 
                                       value="<?= htmlspecialchars($formData['aadhar']) ?>" 
                                       pattern="[0-9]{12}" title="12-digit Aadhar number" required>
                                <div class="input-hint">12 digits only (no spaces or dashes)</div>
                            </div>

                            <div class="mb-3">
                                <label for="pan" class="form-label required-field">PAN Number</label>
                                <input type="text" class="form-control" id="pan" name="pan" 
                                       value="<?= htmlspecialchars($formData['pan']) ?>" 
                                       pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" 
                                       title="PAN format: ABCDE1234F (5 letters, 4 numbers, 1 letter)" required>
                                <div class="input-hint">Format: ABCDE1234F (5 letters, 4 numbers, 1 letter)</div>
                            </div>

                            <div class="mb-4">
                                <label for="city" class="form-label required-field">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?= htmlspecialchars($formData['city']) ?>" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-submit btn-lg">Register Candidate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation for PAN to automatically convert to uppercase
        document.getElementById('pan').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
        
        // Client-side validation for mobile and Aadhar to allow only numbers
        document.getElementById('mobile').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
        
        document.getElementById('aadhar').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
        
        // Ensure video plays even if autoplay is blocked
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video-background');
            const playVideo = () => {
                video.muted = true;
                const playPromise = video.play();
                
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        // Autoplay was prevented, show mute button
                        console.log('Autoplay prevented, showing fallback');
                        video.controls = true;
                    });
                }
            };
            
            // Try to play immediately
            playVideo();
            
            // Also try to play on first user interaction
            document.body.addEventListener('click', function firstInteraction() {
                playVideo();
                document.body.removeEventListener('click', firstInteraction);
            }, { once: true });
        });
    </script>
</body>
</html>