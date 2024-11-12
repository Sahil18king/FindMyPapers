<?php include 'auth_check.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Papers</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <nav>
        <div class="nav-container">
            <h1 class="logo">FindPastPapers</h1>
            <button><a href="logout.php">Logout</a></button>
        </div>
    </nav>

    <main>
        <h1>FindPastPapers</h1>
        <form action="search.php" method="GET" >
            <label for="college">College:</label>
            <select id="college" name="college" required>
                <option value="Pandit Deendayal Energy University">Pandit Deendayal Energy University</option>
            </select>

            <label for="branch">Branch:</label>
            <select id="branch" name="branch" required>
                <option value="Computer Engineering">Computer Engineering</option>
                <option value="Information Communication Technology">Information Communication Technology</option>
                <option value="Mechanical Engineering">Mechanical Engineering</option>
                <option value="Chemical Engineering">Chemical Engineering</option>
                <option value="Civil Engineering">Civil Engineering</option>
                <option value="Petroleum Engineering">Petroleum Engineering</option>
                <option value="Electronic Communication Engineering">Electronic Communication Engineering</option>
                <option value="Automobile">Automobile</option>
            </select>

            <label for="present_year">College Year:</label>
            <select id="present_year" name="present_year" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
            </select>

            <label for="year">Year:</label>
            <select id="year" name="year" required>
                <option value="">Select a year</option>
            </select>

            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="">Select a type</option>
            </select>

            <label for="subjects">Subjects:</label>
            <div id="subject-checkboxes">
                <!-- Checkboxes will be dynamically added here -->
            </div>

            <button type="submit">Search</button>
        </form>

        <div id="results"></div>
    </main>

    <footer>
        <p>&copy; 2024 FindPastPapers. All rights reserved.</p>
    </footer>

    <script>
        // Fetch available years based on selected college, branch, and present year
        function fetchYears() {
            const college = document.getElementById('college').value;
            const branch = document.getElementById('branch').value;
            const presentYear = document.getElementById('present_year').value;

            if (college && branch && presentYear) {
                const xhrYears = new XMLHttpRequest();
                xhrYears.open('GET', `search.php?college=${college}&branch=${branch}&present_year=${presentYear}&fetch_years=true`, true);
                xhrYears.onload = function () {
                    if (xhrYears.status === 200) {
                        const years = JSON.parse(xhrYears.responseText);
                        const yearDropdown = document.getElementById('year');
                        yearDropdown.innerHTML = '<option value="">Select a year</option>';
                        years.forEach(function (year) {
                            const option = document.createElement('option');
                            option.value = year;
                            option.textContent = year;
                            yearDropdown.appendChild(option);
                        });
                    }
                };
                xhrYears.send();
            }
        }

        // Fetch available types based on selected college, branch, college year, and year
        function fetchTypes() {
            const college = document.getElementById('college').value;
            const branch = document.getElementById('branch').value;
            const presentYear = document.getElementById('present_year').value;
            const year = document.getElementById('year').value;

            if (college && branch && presentYear && year) {
                const xhrTypes = new XMLHttpRequest();
                xhrTypes.open('GET', `search.php?college=${college}&branch=${branch}&present_year=${presentYear}&year=${year}&fetch_types=true`, true);
                xhrTypes.onload = function () {
                    if (xhrTypes.status === 200) {
                        const types = JSON.parse(xhrTypes.responseText);
                        const typeDropdown = document.getElementById('type');
                        typeDropdown.innerHTML = '<option value="">Select a type</option>';
                        types.forEach(function (type) {
                            const option = document.createElement('option');
                            option.value = type;
                            option.textContent = type;
                            typeDropdown.appendChild(option);
                        });
                    }
                };
                xhrTypes.send();
            }
        }

        // Fetch subjects based on selected criteria
        function fetchSubjects() {
            const college = document.getElementById('college').value;
            const branch = document.getElementById('branch').value;
            const presentYear = document.getElementById('present_year').value;
            const year = document.getElementById('year').value;
            const type = document.getElementById('type').value;

            if (college && branch && presentYear && year && type) {
                const xhrSubjects = new XMLHttpRequest();
                xhrSubjects.open('GET', `search.php?college=${college}&branch=${branch}&present_year=${presentYear}&year=${year}&type=${type}&fetch_subjects=true`, true);
                xhrSubjects.onload = function () {
                    if (xhrSubjects.status === 200) {
                        const subjects = JSON.parse(xhrSubjects.responseText);
                        const subjectContainer = document.getElementById('subject-checkboxes');
                        subjectContainer.innerHTML = ''; // Clear previous checkboxes

                        subjects.forEach(function (subject) {
                            const label = document.createElement('label');
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'subject[]';
                            checkbox.value = subject;
                            label.appendChild(checkbox);
                            label.appendChild(document.createTextNode(subject));
                            subjectContainer.appendChild(label);
                            subjectContainer.appendChild(document.createElement('br')); // Line break
                        });
                    }
                };
                xhrSubjects.send();
            }
        }

        // Event listeners to trigger fetching types and subjects
        document.getElementById('college').addEventListener('change', fetchYears);
        document.getElementById('branch').addEventListener('change', fetchYears);
        document.getElementById('present_year').addEventListener('change', fetchYears);
        document.getElementById('year').addEventListener('change', fetchTypes);
        document.getElementById('type').addEventListener('change', fetchSubjects);

        // Handle page refresh on back navigation
        if (performance.navigation.type == 2) {
            location.reload();  // Forces a refresh when navigating back
        }

        // Store the search query for persistence
        document.getElementById('college').addEventListener('change', function() {
            localStorage.setItem('college', document.getElementById('college').value);
        });
        document.getElementById('branch').addEventListener('change', function() {
            localStorage.setItem('branch', document.getElementById('branch').value);
        });
        document.getElementById('present_year').addEventListener('change', function() {
            localStorage.setItem('present_year', document.getElementById('present_year').value);
        });

        // Pre-fill form if values are stored
        window.onload = function() {
            if (localStorage.getItem('college')) {
                document.getElementById('college').value = localStorage.getItem('college');
            }
            if (localStorage.getItem('branch')) {
                document.getElementById('branch').value = localStorage.getItem('branch');
            }
            if (localStorage.getItem('present_year')) {
                document.getElementById('present_year').value = localStorage.getItem('present_year');
            }
        }

        // Add a submit event listener to the form to validate subject selection
        document.querySelector('form').addEventListener('submit', function(event) {
            // Get all the checkboxes inside the 'subject-checkboxes' div
            const checkboxes = document.querySelectorAll('#subject-checkboxes input[type="checkbox"]');
    
            // Check if at least one checkbox is selected
            let isChecked = false;
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    isChecked = true;
                }
            });

            // If no checkbox is selected, prevent form submission and show an alert
            if (!isChecked) {
                alert('Please select at least one subject.');
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>

</body>

</html>
