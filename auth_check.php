<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    // Redirect to the login page with an error message if the user is not logged in
    header("Location: login_signup.html?error=Please log in to access this page.");
    exit;
}
?>
