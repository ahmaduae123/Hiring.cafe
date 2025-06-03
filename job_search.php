<?php
session_start();
require 'db.php';
$filters = [];
$query = "SELECT j.*, r.company_name FROM jobs j JOIN recruiter_profiles r ON j.recruiter_id = r.user_id WHERE 1=1";
if (!empty($_GET['category'])) {
    $query .= " AND j.category = ?";
    $filters[] = $_GET['category'];
}
if (!empty($_GET['location'])) {
    $query .= " AND j.location = ?";
    $filters[] = $_GET['location'];
}
if (!empty($_GET['job_type'])) {
    $query .= " AND j.job_type = ?";
    $filters[] = $_GET['job_type'];
}
$stmt = $pdo->prepare($query);
$stmt->execute($filters);
$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Search - Hiring Cafe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f0f4f8, #d9e2ec); }
        .container { max-width: 1200px; margin: 50px auto; padding: 20px; }
        h2 { color: #2c3e50; }
        .filter-form { margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-form input, .filter-form select { padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .filter-form button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .filter-form button:hover { background: #2980b9; }
        .job-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .job-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .job-card h3 { margin: 0; color: #3498db; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2980b9; }
        @media (max-width: 768px) { .job-grid { grid-template-columns: 1fr; } .filter-form { flex-direction: column; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Jobs</h2>
        <form class="filter-form" method="GET">
            <input type="text" name="category" placeholder="Category" value="<?php echo htmlspecialchars($_GET['category'] ?? ''); ?>">
            <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
            <select name="job_type">
                <option value="">All Job Types</option>
                <option value="full_time" <?php if ($_GET['job_type'] == 'full_time') echo 'selected'; ?>>Full Time</option>
                <option value="part_time" <?php if ($_GET['job_type'] == 'part_time') echo 'selected'; ?>>Part Time</option>
                <option value="remote" <?php if ($_GET['job_type'] == 'remote') echo 'selected'; ?>>Remote</option>
            </select>
            <button type="submit">Filter</button>
        </form>
        <div class="job-grid">
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                    <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                    <a href="#" onclick="applyForJob(<?php echo $job['id']; ?>)" class="btn">Apply</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
        function applyForJob(jobId) {
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'candidate'): ?>
                fetch('apply.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'job_id=' + jobId
                }).then(response => response.text()).then(data => {
                    alert(data);
                    redirectTo('job_search.php');
                });
            <?php else: ?>
                alert('Please login as a candidate to apply.');
                redirectTo('login.php');
            <?php endif; ?>
        }
    </script>
</body>
</html>
