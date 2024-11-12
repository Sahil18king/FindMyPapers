<?php
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

// Signup logic
if (isset($_POST['signup'])) {
    $email = $conn->real_escape_string($_POST['signup_email']);
    $password = password_hash($_POST['signup_password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO admin_users (email, Superuser, password) VALUES ('$email', 'NO', '$password')";
    if ($conn->query($sql) === TRUE) {
        header("Location: login_signup.html?message=Signup successful. You can now log in.");
    } else {
        header("Location: login_signup.html?error=Error: " . $conn->error);
    }
    exit;
}

// Login logic
if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['login_email']);
    $password = $_POST['login_password'];
    
    $sql = "SELECT * FROM admin_users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Set session variables for authenticated user
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $email;
            $_SESSION['Superuser'] = $user['Superuser'];
            
            if ($user['Superuser'] === 'YES') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            header("Location: login_signup.html?error=Invalid email or password.");
        }
    } else {
        header("Location: login_signup.html?error=Invalid email or password.");
    }
    exit;
}

$conn->close();
?>
