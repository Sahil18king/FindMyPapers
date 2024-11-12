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
  $year = $_GET['year'];

  $sql = "SELECT DISTINCT subject FROM papers WHERE college = '$college' AND branch = '$branch' AND present_year = '$present_year' AND year = '$year'";
  $result = $conn->query($sql);

  $subjects = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $subjects[] = trim($row['subject']); // Trim any extra whitespace
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
  $subjects = $_GET['subject']; // Array of selected subjects
  $type = $_GET['type'];

  // Sanitize inputs
  $year = $conn->real_escape_string($year);
  $present_year = $conn->real_escape_string($present_year);
  $type = $conn->real_escape_string($type);
  
  // Trim subjects to remove any whitespace issues
  $subjects = array_map(function($subject) use ($conn) {
      return $conn->real_escape_string(trim($subject)); // Trim and sanitize each subject
  }, $subjects);

  // Build the query for multiple subjects
  $subjectList = "'" . implode("','", $subjects) . "'";

  // Execute the query
  $sql = "SELECT link, college, branch, subject, year, present_year FROM papers WHERE year = '$year' AND present_year = '$present_year' AND subject IN ($subjectList) AND type = '$type'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    echo "<div class='results-container'><h2>Search Results</h2><ul class='result-list'>";
    while ($row = $result->fetch_assoc()) {
      echo "<li class='result-item'>";
      echo "<div class='result-header'>";
      echo "<strong>Subject:</strong> " . htmlspecialchars($row['subject']) . "<br>";
      echo "<strong>Year:</strong> " . htmlspecialchars($row['year']) . " - " . htmlspecialchars($row['present_year']) . "<br>";
      echo "<strong>College:</strong> " . htmlspecialchars($row['college']) . " - " . htmlspecialchars($row['branch']) . "<br>";
      echo "</div>";
      echo "<a href='" . $row['link'] . "' target='_blank' class='view-paper'>View Paper</a>";
      echo "</li>";
    }
    echo "</ul></div>";
  } else {
    echo "<p>No papers found for the selected criteria.</p>";
  }
}

$conn->close();
?>