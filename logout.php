<?php
session_start();
session_unset();    // Remove all session variables
session_destroy();  // Destroy the session

header("Location: login_signup.html?message=You have been logged out.");
exit;
?>
