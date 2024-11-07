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

// Handle new entry addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['edit'])) {
    $college = $_POST['college'];
    $branch = $_POST['branch'];
    $year = $_POST['year'];
    $present_year = $_POST['present_year'];
    $subject = $_POST['subject'];
    $type = $_POST['type'];
    $link = $_POST['link'];

    $stmt = $conn->prepare("INSERT INTO papers (college, branch, year, present_year, subject, type, link) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $college, $branch, $year, $present_year, $subject, $type, $link);

    if ($stmt->execute()) {
        echo "<p>New entry added successfully!</p>";
    } else {
        echo "<p>Error adding entry: " . $conn->error . "</p>";
    }

    $stmt->close();
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM papers WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<p>Record with ID $delete_id deleted successfully.</p>";
    } else {
        echo "<p>Error deleting record: " . $conn->error . "</p>";
    }

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
    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this entry?")) {
                window.location.href = "add_entries.php?delete_id=" + id;
            }
        }
    </script>
</head>
<body>
    <h2>Add New Entry</h2>
    <form method="post" action="">
        <label for="college">College:</label>
        <input type="text" id="college" name="college" required><br>

        <label for="branch">Branch:</label>
        <input type="text" id="branch" name="branch" required><br>

        <label for="year">Year:</label>
        <input type="number" id="year" name="year" required><br>

        <label for="present_year">Present Year:</label>
        <input type="number" id="present_year" name="present_year" required><br>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required><br>

        <label for="type">Type:</label>
        <input type="text" id="type" name="type" required><br>

        <label for="link">Link:</label>
        <input type="url" id="link" name="link" required><br>

        <button type="submit">Add Entry</button>
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
            <th>Actions</th>
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
                echo "<td><a href=\"" . $row['link'] . "\">Link</a></td>";
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
</body>
</html>
