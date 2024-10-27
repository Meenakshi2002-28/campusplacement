<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles */
        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            color: white;
        }
        .sidebar a {
            color: #ffffff;
        }
        .sidebar a:hover {
            color: #1abc9c;
        }
        .content {
            padding: 20px;
        }
        .job-table th, .job-table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-dark sidebar py-4">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item mb-3">
                            <a class="nav-link active" href="#">
                                <i class="bi bi-house-door"></i> Home
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link" href="#">
                                <i class="bi bi-briefcase"></i> Jobs
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link" href="#">
                                <i class="bi bi-envelope"></i> Applications
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link" href="#">
                                <i class="bi bi-building"></i> Company
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link" href="#">
                                <i class="bi bi-person"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link" href="#">
                                <i class="bi bi-chat-left-text"></i> Feedback
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link" href="#">
                                <i class="bi bi-box-arrow-right"></i> Log Out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 content">
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <h2>Job List</h2>
                    <div>
                        <button class="btn btn-outline-primary me-2">Jobs</button>
                        <button class="btn btn-outline-secondary">Internships</button>
                        <input type="text" class="form-control d-inline w-auto ms-2" placeholder="Search">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered job-table">
                        <thead class="table-dark">
                            <tr>
                                <th>Company</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Salary</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Example row (you can replace it with dynamic data) -->
                            <tr>
                                <td>ABC Corp</td>
                                <td>Software Engineer</td>
                                <td>Full-Time</td>
                                <td>$70,000</td>
                            </tr>
                            <tr>
                                <td>XYZ Ltd</td>
                                <td>Data Analyst</td>
                                <td>Internship</td>
                                <td>$15/hr</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
