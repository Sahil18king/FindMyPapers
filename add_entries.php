<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $num_entries = (int)$_POST["num_entries"];

    // Insert each entry into the database
    $stmt = $conn->prepare("INSERT INTO papers (college, branch, year, present_year, subject, type, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiss", $college, $branch, $year, $present_year, $subject, $exam_type, $link);

    // Loop through each entry
    for ($i = 1; $i <= $num_entries; $i++) {
        $college = $_POST["college"][$i];
        $branch = $_POST["branch"][$i];
        $year = $_POST["year"][$i];
        $present_year = $_POST["present_year"][$i];
        $subject = $_POST["subject"][$i];
        $exam_type = $_POST["type"][$i];
        $link = $_POST["link"][$i];
        
        // Execute the prepared statement
        $stmt->execute();
    }

    echo "$num_entries entries have been added to the papers table.";

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Multiple Entries</title>
</head>
<body>
    <h2>Enter Number of Entries to Insert</h2>
    <form method="post" action="">
        <label for="num_entries">Number of Entries:</label>
        <input type="number" id="num_entries" name="num_entries" required>
        <button type="submit" onclick="generateFormFields()">Generate Form</button>
    </form>

    <form method="post" action="add_entries.php" id="entriesForm">
        <div id="formFields"></div>
        <button type="submit">Submit Entries</button>
    </form>

    <script>
        function generateFormFields() {
            event.preventDefault();
            let numEntries = document.getElementById("num_entries").value;
            let formFields = document.getElementById("formFields");
            formFields.innerHTML = "";

            for (let i = 1; i <= numEntries; i++) {
                formFields.innerHTML += `
                    <h3>Entry ${i}</h3>
                    <label>College:</label>
                    <input type="text" name="college[${i}]" required><br>

                    <label>Branch:</label>
                    <input type="text" name="branch[${i}]" required><br>

                    <label>Year:</label>
                    <input type="number" name="year[${i}]" required><br>

                    <label>Present Year:</label>
                    <input type="number" name="present_year[${i}]" required><br>

                    <label>Subject:</label>
                    <input type="text" name="subject[${i}]" required><br>

                    <label>Type:</label>
                    <input type="text" name="type[${i}]" required><br>

                    <label>Link:</label>
                    <input type="text" name="link[${i}]" required><br><br>
                `;
            }
        }
    </script>
</body>
</html>
