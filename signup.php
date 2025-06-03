<?php
session_start();
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user_type = $_POST['user_type'];
    $stmt = $pdo->prepare("INSERT INTO users (email, password, user_type) VALUES (?, ?, ?)");
    if ($stmt->execute([$email, $password, $user_type])) {
        echo "<script>alert('Registration successful! Please login.'); redirectTo('login.php');</script>";
    } else {
        echo "<script>alert('Registration failed.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Hiring Cafe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f0f4f8, #d9e2ec); }
        .container { max-width: 400px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #2c3e50; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        a { color: #3498db; text-decoration: none; display: block; text-align: center; margin-top: 10px; }
        a:hover { color: #2980b9; }
        @media (max-width: 768px) { .container { margin: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Signup</h2>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="user_type">User Type</label>
                <select id="user_type" name="user_type" required>
                    <option value="candidate">Candidate</option>
                    <option value="recruiter">Recruiter</option>
                </select>
            </div>
            <button type="submit">Signup</button>
        </form>
        <a href="#" onclick="redirectTo('login.php')">Already have an account? Login</a>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
