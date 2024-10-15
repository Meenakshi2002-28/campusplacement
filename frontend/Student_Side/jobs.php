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
            font-size: 18.5px;
        }

        .job-table td {
            font-size: 17.5px;
            word-break: break-all;
        }

        .job-table tr:hover {
            background-color: #f1f1f1;
        }
        .job-table a {
        color: inherit;            /* Inherit the color from the parent element */
        text-decoration: none;     /* Remove underline */
        cursor: pointer;           /* Change cursor to pointer */
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
    </style>
</head>
<body>
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
    <button class="tab-button active" data-tab="jobs" onclick="showTab('jobs')">JOBS</button>
    <button class="tab-button" data-tab="internships" onclick="showTab('internships')">INTERNSHIPS</button>
    <input type="text" class="search-bar" placeholder="Search" id="searchInput" onkeyup="filterJobs()">
</div>

<div id="jobsSection">
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

<div id="internshipsSection" style="display: none;">
    <table class="job-table" id="internshipTable">
        <thead>
            <tr>
                <th>Company</th>
                <th>Title</th>
                <th>Type</th>
                <th>Salary</th>
            </tr>
        </thead>
        <tbody id="internshipTableBody">
            <!-- Internships will be dynamically inserted here -->
        </tbody>
    </table>
</div>
    <script src="script.js">
    </script> <!-- Link to your JavaScript file -->
</body>
</html>
