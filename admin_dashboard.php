<?php include 'auth_check.php'; ?>
<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['Superuser'] !== 'YES') {
    header("Location: login_signup.php");
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
    <nav>
        <button><a href="admin_dashboard.php">Dashboard</a></button>
        <button><a href="add_entries.php">Add Entries</a></button>
        <button><a href="logout.php">Logout</a></button>
    </nav>
    <h1>Welcome, <?php echo $_SESSION['user_email']; ?>!</h1>
    <p>This is the admin dashboard where you can manage website data.</p>
</body>
</html>
