    <?php
    $servername = "localhost";
    $username = "root";
    $password = "1234"; // Your database password
    $dbname = "vsm"; // Your database name

    $conn = new mysqli($servername, $username, $password, $dbname,);
$conn = new mysqli($servername, $username, $password, $dbname);

// Add error reporting for debugging purposes
if ($conn->connect_error) {
    error_log("Database connection error: " . $conn->connect_error); // Log the error for debugging
    die("Connection failed: Please check database connection parameters.");
}

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
