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

        <!-- Message about upcoming papers -->
        <div id="update-message">
            <p><strong>Other papers will be updated soon!</strong></p>
        </div>
        <h1>FindPastPapers</h1>
        <form action="search.php" method="GET">
            <label for="college">College:</label>
            <select id="college" name="college" required onchange="fetchBranchesAndYears()">
                <option value="">Select a college</option>
            </select>

            <label for="branch">Branch:</label>
            <select id="branch" name="branch" required onchange="fetchYears()">
                <option value="">Select a branch</option>
            </select>

            <label for="present_year">College Year:</label>
            <select id="present_year" name="present_year" required onchange="fetchYears()">
                <option value="">Select a college year</option>
            </select>

            <label for="year">Year:</label>
            <select id="year" name="year" required onchange="fetchTypes()">
                <option value="">Select a year</option>
            </select>

            <label for="type">Type:</label>
            <select id="type" name="type" required onchange="fetchSubjects()">
                <option value="">Select a type</option>
            </select>

            <label for="subjects">Subjects:</label>
            <div id="subject-checkboxes"></div>

            <button type="submit">Search</button>
        </form>

        <div id="results"></div>

        <!-- Message and heart link to WhatsApp -->
        <div id="contact-message">
            <p>If you have any queries or papers which are not present here, feel free to message me 💖</p>
            <a href="https://wa.me/+919428235545?text=Hello,%20I%20have%20some%20papers%20to%20share." target="_blank">
                <button>Message on WhatsApp</button>
            </a>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 FindPastPapers. All rights reserved.</p>
    </footer>

    <script>
        // Load dynamic dropdowns on page load
        window.onload = function() {
            fetchColleges();
        }

        // Fetch colleges
        function fetchColleges() {
            fetch('search.php?fetch_colleges=true')
                .then(response => response.json())
                .then(data => {
                    const collegeDropdown = document.getElementById('college');
                    collegeDropdown.innerHTML = '<option value="">Select a college</option>';
                    data.forEach(college => {
                        const option = document.createElement('option');
                        option.value = college;
                        option.textContent = college;
                        collegeDropdown.appendChild(option);
                    });
                });
        }

        // Fetch branches and years based on selected college
        function fetchBranchesAndYears() {
            const college = document.getElementById('college').value;
            if (college) {
                fetch(`search.php?college=${college}&fetch_branches_years=true`)
                    .then(response => response.json())
                    .then(data => {
                        const branchDropdown = document.getElementById('branch');
                        const yearDropdown = document.getElementById('present_year');

                        // Populate branches
                        branchDropdown.innerHTML = '<option value="">Select a branch</option>';
                        data.branches.forEach(branch => {
                            const option = document.createElement('option');
                            option.value = branch;
                            option.textContent = branch;
                            branchDropdown.appendChild(option);
                        });

                        // Populate years
                        yearDropdown.innerHTML = '<option value="">Select a college year</option>';
                        data.years.forEach(year => {
                            const option = document.createElement('option');
                            option.value = year;
                            option.textContent = year;
                            yearDropdown.appendChild(option);
                        });
                    });
            }
        }

        // Fetch available years for selected college, branch, and present year
        function fetchYears() {
            const college = document.getElementById('college').value;
            const branch = document.getElementById('branch').value;
            const presentYear = document.getElementById('present_year').value;

            if (college && branch && presentYear) {
                fetch(`search.php?college=${college}&branch=${branch}&present_year=${presentYear}&fetch_years=true`)
                    .then(response => response.json())
                    .then(years => {
                        const yearDropdown = document.getElementById('year');
                        yearDropdown.innerHTML = '<option value="">Select a year</option>';
                        years.forEach(year => {
                            const option = document.createElement('option');
                            option.value = year;
                            option.textContent = year;
                            yearDropdown.appendChild(option);
                        });
                    });
            }
        }

        // Fetch available types
        function fetchTypes() {
            const college = document.getElementById('college').value;
            const branch = document.getElementById('branch').value;
            const presentYear = document.getElementById('present_year').value;
            const year = document.getElementById('year').value;

            if (college && branch && presentYear && year) {
                fetch(`search.php?college=${college}&branch=${branch}&present_year=${presentYear}&year=${year}&fetch_types=true`)
                    .then(response => response.json())
                    .then(types => {
                        const typeDropdown = document.getElementById('type');
                        typeDropdown.innerHTML = '<option value="">Select a type</option>';
                        types.forEach(type => {
                            const option = document.createElement('option');
                            option.value = type;
                            option.textContent = type;
                            typeDropdown.appendChild(option);
                        });
                    });
            }
        }

        // Fetch subjects
        function fetchSubjects() {
            const college = document.getElementById('college').value;
            const branch = document.getElementById('branch').value;
            const presentYear = document.getElementById('present_year').value;
            const year = document.getElementById('year').value;
            const type = document.getElementById('type').value;

            if (college && branch && presentYear && year && type) {
                fetch(`search.php?college=${college}&branch=${branch}&present_year=${presentYear}&year=${year}&type=${type}&fetch_subjects=true`)
                    .then(response => response.json())
                    .then(subjects => {
                        const subjectContainer = document.getElementById('subject-checkboxes');
                        subjectContainer.innerHTML = '';
                        subjects.forEach(subject => {
                            const label = document.createElement('label');
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'subject[]';
                            checkbox.value = subject;
                            label.appendChild(checkbox);
                            label.appendChild(document.createTextNode(subject));
                            subjectContainer.appendChild(label);
                            subjectContainer.appendChild(document.createElement('br'));
                        });
                    });
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
