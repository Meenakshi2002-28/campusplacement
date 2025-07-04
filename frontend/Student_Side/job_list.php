

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job List </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: white;
        }

        .sidebar {
            width: 198px;
            height: 610px;
            position: fixed;
            left: 10px;
            top: 85px;
            background-color: #2F5597;
            color: white;
            padding: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 15px;
            font-size: 22px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            border-left: 3px solid #ffffff;
            background: #1e165f;
        }

        img {
            height: 40px;
            width: auto;
        }

        .container {
            padding: 5px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .icon {
            margin-left: 1px;
        }

        .main-content {
            flex-grow: 1;
            padding: 40px;
            background-color: white;
        }

        .tabs {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-left: 280px;
            padding-right: 1px;
            word-break: keep-all;
        }

        .tab-button {
            background-color: white;
            border: 1px solid #000000;
            padding: 0px;
            border-radius: 50px;
            margin-right: 50px;
            cursor: pointer;
            font-size: 16px;
            height: 26px;
            width: 130px;
        }

        .tab-button.active {
            background-color: #1c4a82;
            color: white;
        }

        .search-bar {
            flex-grow: 1;
            padding: 4px;
            border: 1px solid black;
            border-radius: 10px;
            margin-left: 150px;
            font-size: 16px;
        }

        .job-table {
            width: 100%;
            border-collapse: collapse;
        }

        .job-table th, .job-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            padding-left: 200px;
        }

        .job-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid black;
        }

        .job-table td {
            font-size: 15px;
            word-break: break-all;
        }

        .job-table tr:hover {
            background-color: #f1f1f1;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #2F5597;
            min-width: 150px;
            z-index: 1;
            top: 55px;
            border-radius: 3px;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #1e165f;
        }

        .logout a {
            font-size: 20px;
            margin-top: 210px;
        }
        .logo-container {
        position: absolute;
        top: 10px;
        left: 10px;
        }
        .logo {
        height: 50px;
        width: auto;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="../images/logo1.png" alt="Logo" class="logo">
    </div>
    <div class="container">
        <img src="../images/profile.png" alt="Profile Icon" class="icon" id="profileIcon" onclick="triggerFileInput()">
        <input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="changeProfilePicture(event)">
        
        <i class="fas fa-caret-down fa-2x" aria-hidden="true" onclick="toggleDropdown()"></i>
        <div id="dropdownMenu" class="dropdown-content">
            <a href="../Student_Side/profile_std.php"><i class="fa fa-fw fa-user"></i> Profile</a>
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <div class="sidebar">
        <a href="dashboard_std.php"><i class="fa fa-fw fa-home"></i> Home</a>
        <a href="jobs.php"><i class="fa fa-fw fa-search"></i> Jobs</a>
        <a href="#applications"><i class="fa fa-fw fa-envelope"></i> Applications</a>
        <a href="company.html"><i class="fa fa-fw fa-building"></i> Company</a>
        <a href="../profile_redirect.php"><i class="fa fa-fw fa-user"></i> Profile</a>
        <a href="feedback.html"><i class="fa fa-fw fa-comment"></i> Feedback</a>
        <div class="logout">
            <a href="../logout.php"><i class="fas fa-power-off"></i> Log Out</a>
        </div>
    </div>

    <div class="main-content">
        <div class="tabs">
            <button class="tab-button active">JOBS</button>
            <button class="tab-button">INTERNSHIPS</button>
            <input type="text" class="search-bar" placeholder="Search" id="searchInput" onkeyup="filterJobs()">
        </div>

        <!-- Job List Table -->
        <table class="job-table" id="jobTable">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Salary</th>
                </tr>
            </thead>
            <tbody id="jobTableBody">
                <!-- Jobs will be dynamically inserted here -->
            </tbody>
        </table>
    </div>

    <script>
         // Change profile image
    function triggerFileInput() {
            document.getElementById('fileInput').click();
        }

    function changeProfilePicture(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('sidebarProfilePicture').src = e.target.result; // Update the profile image in sidebar
                document.getElementById('profileIcon').src = e.target.result; // Update profile icon
            };
            reader.readAsDataURL(file); // Read the image file
        }
    }
    let dropdownOpen = false;
    function toggleDropdown() {
        const dropdown = document.getElementById("dropdownMenu");
        dropdownOpen = !dropdownOpen;
        dropdown.style.display = dropdownOpen ? "block" : "none";
    }

    function goToProfile() {
        showSection('personal'); // Redirect to profile section
        toggleDropdown(); // Close the dropdown after redirection
    }
        // Fetch job data from the server
        async function fetchJobs() {
            try {
                const response = await fetch('get_jobs.php'); // Example API endpoint
                const jobs = await response.json(); // Assuming JSON response

                const jobTableBody = document.getElementById('jobTableBody');
                jobTableBody.innerHTML = ''; // Clear existing table rows

                jobs.forEach(job => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                         <td><a href="job_desc.php?job_id=${job.id}">${job.company}</a></td>
        <td>${job.title}</td>
        <td>${job.type}</td>
        <td>${job.salary}</td>
                    `;
                    jobTableBody.appendChild(row);
                });
            } catch (error) {
                console.error('Error fetching jobs:', error);
            }
        }

        // Filter jobs based on the search input
        function filterJobs() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('#jobTable tbody tr');

            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                const company = cells[0].textContent.toLowerCase();
                const title = cells[1].textContent.toLowerCase();
                const type = cells[2].textContent.toLowerCase();
                const salary = cells[3].textContent.toLowerCase();

                if (company.includes(searchValue) || title.includes(searchValue) || type.includes(searchValue) || salary.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Initialize the job list on page load
        document.addEventListener('DOMContentLoaded', fetchJobs);
    </script>
</body>
</html>