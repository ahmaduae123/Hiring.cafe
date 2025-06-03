<?php
session_start();
require 'db.php';
$jobs = $pdo->query("SELECT j.*, r.company_name FROM jobs j JOIN recruiter_profiles r ON j.recruiter_id = r.user_id ORDER BY posted_at DESC LIMIT 6")->fetchAll();
$candidates = $pdo->query("SELECT c.*, u.email FROM candidate_profiles c JOIN users u ON c.user_id = u.id ORDER BY RAND() LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiring Cafe - Find Jobs & Talent</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f0f4f8, #d9e2ec); }
        header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        header h1 { margin: 0; font-size: 2.5em; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
        nav a:hover { color: #3498db; }
        .container { max-width: 1200px; margin: 20px auto; padding: 0 20px; }
        .section { margin: 40px 0; }
        .section h2 { color: #2c3e50; font-size: 1.8em; }
        .job-grid, .candidate-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .job-card, .candidate-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .job-card:hover, .candidate-card:hover { transform: translateY(-5px); }
        .job-card h3, .candidate-card h3 { margin: 0; color: #3498db; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2980b9; }
        @media (max-width: 768px) { .job-grid, .candidate-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <h1>Hiring Cafe</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="#" onclick="redirectTo('job_search.php')">Jobs</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" onclick="redirectTo('profile.php')">Profile</a>
                <a href="#" onclick="redirectTo('logout.php')">Logout</a>
            <?php else: ?>
                <a href="#" onclick="redirectTo('login.php')">Login</a>
                <a href="#" onclick="redirectTo('signup.php')">Signup</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
        <div class="section">
            <h2>Trending Jobs</h2>
            <div class="job-grid">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card">
                        <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type']); ?></p>
                        <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?></p>
                        <a href="#" onclick="redirectTo('job_search.php?id=<?php echo $job['id']; ?>')" class="btn">View Job</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="section">
            <h2>Top Candidates</h2>
            <div class="candidate-grid">
                <?php foreach ($candidates as $candidate): ?>
                    <div class="candidate-card">
                        <h3><?php echo htmlspecialchars($candidate['full_name']); ?></h3>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($candidate['email']); ?></p>
                        <p><strong>Skills:</strong> <?php echo htmlspecialchars($candidate['skills']); ?></p>
                        <p><strong>Experience:</strong> <?php echo htmlspecialchars($candidate['experience']); ?> years</p>
                        <a href="#" onclick="redirectTo('profile.php?id=<?php echo $candidate['user_id']; ?>')" class="btn">View Profile</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
