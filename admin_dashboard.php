<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_entries.php">Add Entries</a>
        <a href="admin_logout.php">Logout</a>
    </nav>

    <h1>Welcome, <?php echo $_SESSION['admin_email']; ?>!</h1>
    <p>This is the admin dashboard where you can manage website data.</p>
</body>
</html>
