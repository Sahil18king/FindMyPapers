<?php
// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=your_database_name', 'username', 'password');

$college = $_GET['college'];
$branch = $_GET['branch'];
$year = $_GET['year'];
$present_year = $_GET['present_year'];

// Query to fetch subjects
$stmt = $pdo->prepare("SELECT DISTINCT subject FROM your_table_name WHERE college = ? AND branch = ? AND year = ? AND present_year = ?");
$stmt->execute([$college, $branch, $year, $present_year]);

$subjects = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo json_encode($subjects);
?>
