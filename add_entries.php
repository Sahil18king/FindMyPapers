<?php
session_start();

// // Check if admin is logged in
// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: admin_login.php");
//     exit;
// }

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

// Handle new entries
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['edit'])) {
    $num_entries = (int)$_POST["num_entries"];
    $college = $_POST["college"] ?: null;
    $branch = $_POST["branch"] ?: null;
    $year = !empty($_POST["year"]) ? (int)$_POST["year"] : null;
    $present_year = !empty($_POST["present_year"]) ? (int)$_POST["present_year"] : null;
    $subject = $_POST["subject"] ?: null;
    $exam_type = $_POST["type"] ?: null;
    $link = $_POST["link"] ?: null;

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO papers (college, branch, year, present_year, subject, type, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiss", $college, $branch, $year, $present_year, $subject, $exam_type, $link);

    // Insert the specified number of entries
    for ($i = 0; $i < $num_entries; $i++) {
        $stmt->execute();
    }

    echo "$num_entries entries have been added to the papers table.";
    $stmt->close();
}

// Pagination setup
$entries_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $entries_per_page;

// Fetch total count of entries
$result = $conn->query("SELECT COUNT(*) AS total FROM papers");
$total_entries = $result->fetch_assoc()['total'];
$total_pages = ceil($total_entries / $entries_per_page);

// Sorting setup
$order_by = isset($_GET['order_by']) && $_GET['order_by'] === 'latest' ? 'DESC' : 'ASC';

// Fetch entries with pagination and ordering
$sql = "SELECT * FROM papers ORDER BY id $order_by LIMIT $offset, $entries_per_page";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Papers</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Add Multiple Entries</h2>
    <form method="post" action="">
        <label for="num_entries">Number of Entries:</label>
        <input type="number" id="num_entries" name="num_entries" min="1" required><br><br>

        <label>College:</label>
        <input type="text" name="college"><br><br>

        <label>Branch:</label>
        <input type="text" name="branch"><br><br>

        <label>Year:</label>
        <input type="number" name="year"><br><br>

        <label>Present Year:</label>
        <input type="number" name="present_year"><br><br>

        <label>Subject:</label>
        <input type="text" name="subject"><br><br>

        <label>Type:</label>
        <input type="text" name="type"><br><br>

        <label>Link:</label>
        <input type="text" name="link"><br><br>

        <button type="submit">Submit Entries</button>
    </form>

    <h1>Database Entries</h1>
    <form method="GET" action="">
        <label for="order_by">Sort By:</label>
        <select name="order_by" id="order_by" onchange="this.form.submit()">
            <option value="latest" <?php if($order_by == 'DESC') echo 'selected'; ?>>Latest Entries</option>
            <option value="oldest" <?php if($order_by == 'ASC') echo 'selected'; ?>>Oldest Entries</option>
        </select>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>College</th>
            <th>Branch</th>
            <th>Year</th>
            <th>Present Year</th>
            <th>Subject</th>
            <th>Type</th>
            <th>Link</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['college'] . "</td>";
                echo "<td>" . $row['branch'] . "</td>";
                echo "<td>" . $row['year'] . "</td>";
                echo "<td>" . $row['present_year'] . "</td>";
                echo "<td>" . $row['subject'] . "</td>";
                echo "<td>" . $row['type'] . "</td>";
                echo "<td>" . $row['link'] . "</td>";
                echo "<td>
                        <a href=\"edit_entry.php?edit_id=" . $row['id'] . "\">Edit</a> | 
                        <a href=\"javascript:void(0);\" onclick=\"confirmDelete(" . $row['id'] . ")\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No entries found.</td></tr>";
        }
        ?>
    </table>

    <!-- Pagination -->
    <div>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&order_by=<?php echo $order_by; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this entry?")) {
                window.location.href = "add_entries.php?delete_id=" + id;
            }
        }
    </script>
</body>
</html>
