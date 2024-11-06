<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_papers";




// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Fetch admin data
    $sql = "SELECT * FROM admin_users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify the password
        if ($password == $admin['password']) {
            // Password is correct, start session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            exit;
        } else {
            echo "<p>Invalid email or password.</p>";
        }
    } else {
        echo "<p>Invalid email or password.</p>";
    }
}

$conn->close();
?>
