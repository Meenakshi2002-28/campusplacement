// Fetch job data from the server
async function fetchJobs() {
    try {
        const response = await fetch('get_jobs.php'); // Endpoint to fetch job data
        const jobs = await response.json(); // Assuming JSON response

        const jobTableBody = document.getElementById('jobTableBody');
        jobTableBody.innerHTML = ''; // Clear existing table rows

        jobs.forEach(job => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><a href="job_description.php?job_id=${job.id}">${job.company}</a></td>
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

// Initialize the job list on page load
document.addEventListener('DOMContentLoaded', fetchJobs);
// Function to show selected tab (jobs or internships)
function showTab(tab) {
    // Hide both sections initially
    document.getElementById('jobsSection').style.display = 'none';
    document.getElementById('internshipsSection').style.display = 'none';

    // Remove 'active' class from both buttons
    document.querySelector('.tab-button.active').classList.remove('active');

    // Show the selected section and add 'active' class to the clicked button
    if (tab === 'jobs') {
        document.getElementById('jobsSection').style.display = 'block';
        document.querySelector('.tab-button[data-tab="jobs"]').classList.add('active');
    } else if (tab === 'internships') {
        document.getElementById('internshipsSection').style.display = 'block';
        document.querySelector('.tab-button[data-tab="internships"]').classList.add('active');
    }
}