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

// Initialize the job list on page load
document.addEventListener('DOMContentLoaded', fetchJobs);
