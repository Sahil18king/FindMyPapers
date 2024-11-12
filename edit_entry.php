<?php
session_start();

include 'auth.php'; 

// Database connection details
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

// Check if edit_id is provided
if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];

    // Fetch data for the record to edit
    $result = $conn->query("SELECT * FROM papers WHERE id = $edit_id");
    $row = $result->fetch_assoc();
} else {
    die("No edit_id provided.");
}

// Handle edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $college = $_POST['college'] ?: null;
    $branch = $_POST['branch'] ?: null;
    $year = !empty($_POST['year']) ? (int)$_POST['year'] : null;
    $present_year = !empty($_POST['present_year']) ? (int)$_POST['present_year'] : null;
    $subject = $_POST['subject'] ?: null;
    $exam_type = $_POST['type'] ?: null;
    $link = $_POST['link'] ?: null;

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("UPDATE papers SET college=?, branch=?, year=?, present_year=?, subject=?, type=?, link=? WHERE id=?");
    $stmt->bind_param("ssiiissi", $college, $branch, $year, $present_year, $subject, $exam_type, $link, $id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record updated successfully.";
        header("Location: add_entries.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }

    // Close statement
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Entry</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Entry</h1>

    <?php if (isset($row)): ?>
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

            <label>College:</label>
            <input type="text" name="college" value="<?php echo $row['college']; ?>"><br><br>

            <label>Branch:</label>
            <input type="text" name="branch" value="<?php echo $row['branch']; ?>"><br><br>

            <label>Year:</label>
            <input type="number" name="year" value="<?php echo $row['year']; ?>"><br><br>

            <label>Present Year:</label>
            <input type="number" name="present_year" value="<?php echo $row['present_year']; ?>"><br><br>

            <label>Subject:</label>
            <input type="text" name="subject" value="<?php echo $row['subject']; ?>"><br><br>

            <label>Type:</label>
            <input type="text" name="type" value="<?php echo $row['type']; ?>"><br><br>

            <label>Link:</label>
            <input type="text" name="link" value="<?php echo $row['link']; ?>"><br><br>

            <button type="submit" name="edit">Update Entry</button>
        </form>
    <?php else: ?>
        <p>Record not found.</p>
    <?php endif; ?>
</body>
</html>
 