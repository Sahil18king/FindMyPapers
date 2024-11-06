<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_papers";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if fetching subjects
if (isset($_GET['fetch_subjects'])) {
  $college = $_GET['college'];
  $branch = $_GET['branch'];
  $present_year = $_GET['present_year'];
  $year = $_GET['year']; // New line to get the selected year

  $sql = "SELECT DISTINCT subject FROM papers WHERE college = '$college' AND branch = '$branch' AND present_year = '$present_year' AND year = '$year'";
  $result = $conn->query($sql);

  $subjects = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $subjects[] = $row['subject'];
    }
  }

  echo json_encode($subjects);
  $conn->close();
  exit;
}

// Check if fetching years
if (isset($_GET['fetch_years'])) {
  $college = $_GET['college'];
  $branch = $_GET['branch'];
  $present_year = $_GET['present_year'];

  // Updated SQL query to also include present_year
  $sql = "SELECT DISTINCT year FROM papers WHERE college = '$college' AND branch = '$branch' AND present_year = '$present_year'";
  $result = $conn->query($sql);

  $years = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $years[] = $row['year'];
    }
  }

  echo json_encode($years);
  $conn->close();
  exit;
}

// Original search functionality (if the form is submitted)
if (isset($_GET['year']) && isset($_GET['present_year']) && isset($_GET['subject']) && isset($_GET['type'])) {
  $year = $_GET['year'];
  $present_year = $_GET['present_year'];
  $subject = $_GET['subject'];
  $type = $_GET['type'];

  $sql = "SELECT link FROM papers WHERE year = '$year' AND present_year = '$present_year' AND subject = '$subject' AND type = '$type'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    echo "<h2>Results:</h2><ul>";
    while ($row = $result->fetch_assoc()) {
      echo "<li><a href='" . $row['link'] . "' target='_blank'>View Paper</a></li>";
    }
    echo "</ul>";
  } else {
    echo "<p>No papers found for the selected criteria.</p>";
  }
}

$conn->close();
?>
