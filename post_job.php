<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'recruiter') {
    echo "<script>redirectTo('login.php');</script>";
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $job_type = $_POST['job_type'];
    $salary_range = $_POST['salary_range'];
    $stmt = $pdo->prepare("INSERT INTO jobs (recruiter_id, title, description, category, location, job_type, salary_range) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $description, $category, $location, $job_type, $salary_range]);
    echo "<script>alert('Job posted successfully!'); redirectTo('index.php');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - Hiring Cafe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f0f4f8, #d9e2ec); }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #2c3e50; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        @media (max-width: 768px) { .container { margin: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Post a Job</h2>
        <form method="POST">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" name="category">
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location">
            </div>
            <div class="form-group">
                <label for="job_type">Job Type</label>
                <select id="job_type" name="job_type" required>
                    <option value="full_time">Full Time</option>
                    <option value="part_time">Part Time</option>
                    <option value="remote">Remote</option>
                </select>
            </div>
            <div class="form-group">
                <label for="salary_range">Salary Range</label>
                <input type="text" id="salary_range" name="salary_range">
            </div>
            <button type="submit">Post Job</button>
        </form>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
