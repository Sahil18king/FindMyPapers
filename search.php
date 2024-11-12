<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_papers";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


// Fetch colleges
if (isset($_GET['fetch_colleges'])) {
  $sql = "SELECT DISTINCT college FROM papers";
  $result = $conn->query($sql);
  $colleges = [];
  while ($row = $result->fetch_assoc()) {
      $colleges[] = $row['college'];
  }
  echo json_encode($colleges);
  $conn->close();
  exit;
}

// Fetch branches and college years based on selected college
if (isset($_GET['fetch_branches_years'])) {
  $college = $_GET['college'];
  $sqlBranches = "SELECT DISTINCT branch FROM papers WHERE college = '$college'";
  $sqlYears = "SELECT DISTINCT present_year FROM papers WHERE college = '$college'";

  $branches = [];
  $years = [];

  $resultBranches = $conn->query($sqlBranches);
  while ($row = $resultBranches->fetch_assoc()) {
      $branches[] = $row['branch'];
  }

  $resultYears = $conn->query($sqlYears);
  while ($row = $resultYears->fetch_assoc()) {
      $years[] = $row['present_year'];
  }

  echo json_encode(['branches' => $branches, 'years' => $years]);
  $conn->close();
  exit;
}

// Fetch available years based on selected college, branch, and present year
if (isset($_GET['fetch_years'])) {
  $college = $_GET['college'];
  $branch = $_GET['branch'];
  $present_year = $_GET['present_year'];

  $sql = "SELECT DISTINCT year FROM papers WHERE college = '$college' AND branch = '$branch' AND present_year = '$present_year'";
  $result = $conn->query($sql);

  $years = [];
  while ($row = $result->fetch_assoc()) {
      $years[] = $row['year'];
  }
  echo json_encode($years);
  $conn->close();
  exit;
}
// Check if fetching types
if (isset($_GET['fetch_types'])) {
  $college = $_GET['college'];
  $branch = $_GET['branch'];
  $present_year = $_GET['present_year'];
  $year = $_GET['year'];

  $sql = "SELECT DISTINCT type FROM papers WHERE college = '$college' AND branch = '$branch' AND present_year = '$present_year' AND year = '$year'";
  $result = $conn->query($sql);

  $types = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $types[] = trim($row['type']);
    }
  }

  echo json_encode($types);
  $conn->close();
  exit;
}

// Check if fetching subjects
if (isset($_GET['fetch_subjects'])) {
  $college = $_GET['college'];
  $branch = $_GET['branch'];
  $present_year = $_GET['present_year'];
  $year = $_GET['year'];
  $type = $_GET['type'];

  $sql = "SELECT DISTINCT subject FROM papers WHERE college = '$college' AND branch = '$branch' AND present_year = '$present_year' AND year = '$year' AND type = '$type'";
  $result = $conn->query($sql);

  $subjects = [];
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $subjects[] = trim($row['subject']);
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
  $subjects = $_GET['subject'];
  $type = $_GET['type'];

  $year = $conn->real_escape_string($year);
  $present_year = $conn->real_escape_string($present_year);
  $type = $conn->real_escape_string($type);

  $subjects = array_map(function($subject) use ($conn) {
      return $conn->real_escape_string(trim($subject));
  }, $subjects);

  $subjectList = "'" . implode("','", $subjects) . "'";

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


<!-- Add your CSS to style the page -->
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f4f4f4;
    }
    
    .results-container {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }
    
    .result-list {
        list-style: none;
        padding: 0;
    }
    
    .result-item {
        background-color: #fafafa;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: background-color 0.3s ease;
    }
    
    .result-item:hover {
        background-color: #e9f5ff;
    }
    
    .result-header {
        margin-bottom: 10px;
    }
    
    .view-paper {
        display: inline-block;
        background-color: #00677b;
        color: white;
        padding: 8px 16px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    
    .view-paper:hover {
        background-color: #00677b;
    }
    
    p {
        font-size: 16px;
        color: #555;
    }
</style>
