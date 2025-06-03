<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>redirectTo('login.php');</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($user_type == 'candidate') {
        $full_name = $_POST['full_name'];
        $skills = $_POST['skills'];
        $experience = $_POST['experience'];
        $resume = $_FILES['resume']['name'];
        $video = $_FILES['video']['name'];
        if ($resume) move_uploaded_file($_FILES['resume']['tmp_name'], "uploads/" . $resume);
        if ($video) move_uploaded_file($_FILES['video']['tmp_name'], "uploads/" . $video);
        $stmt = $pdo->prepare("INSERT INTO candidate_profiles (user_id, full_name, skills, experience, resume_path, video_path) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE full_name = ?, skills = ?, experience = ?, resume_path = ?, video_path = ?");
        $stmt->execute([$user_id, $full_name, $skills, $experience, $resume, $video, $full_name, $skills, $experience, $resume, $video]);
    } else {
        $company_name = $_POST['company_name'];
        $company_description = $_POST['company_description'];
        $logo = $_FILES['logo']['name'];
        if ($logo) move_uploaded_file($_FILES['logo']['tmp_name'], "uploads/" . $logo);
        $stmt = $pdo->prepare("INSERT INTO recruiter_profiles (user_id, company_name, company_description, logo_path) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE company_name = ?, company_description = ?, logo_path = ?");
        $stmt->execute([$user_id, $company_name, $company_description, $logo, $company_name, $company_description, $logo]);
    }
    echo "<script>alert('Profile updated successfully!');</script>";
}

$profile = $user_type == 'candidate' ? $pdo->query("SELECT * FROM candidate_profiles WHERE user_id = $user_id")->fetch() : $pdo->query("SELECT * FROM recruiter_profiles WHERE user_id = $user_id")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Hiring Cafe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f0f4f8, #d9e2ec); }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #2c3e50; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .profile-info { margin-top: 20px; }
        .profile-info p { margin: 5px 0; }
        @media (max-width: 768px) { .container { margin: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2><?php echo $user_type == 'candidate' ? 'Candidate Profile' : 'Recruiter Profile'; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <?php if ($user_type == 'candidate'): ?>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="skills">Skills (comma-separated)</label>
                    <input type="text" id="skills" name="skills" value="<?php echo htmlspecialchars($profile['skills'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="experience">Experience (years)</label>
                    <input type="number" id="experience" name="experience" value="<?php echo htmlspecialchars($profile['experience'] ?? 0); ?>">
                </div>
                <div class="form-group">
                    <label for="resume">Upload Resume</label>
                    <input type="file" id="resume" name="resume" accept=".pdf">
                </div>
                <div class="form-group">
                    <label for="video">Upload Video Intro</label>
                    <input type="file" id="video" name="video" accept=".mp4,.mov">
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($profile['company_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="company_description">Company Description</label>
                    <textarea id="company_description" name="company_description"><?php echo htmlspecialchars($profile['company_description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="logo">Upload Logo</label>
                    <input type="file" id="logo" name="logo" accept=".png,.jpg,.jpeg">
                </div>
            <?php endif; ?>
            <button type="submit">Update Profile</button>
        </form>
        <div class="profile-info">
            <?php if ($profile): ?>
                <?php if ($user_type == 'candidate'): ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($profile['full_name']); ?></p>
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($profile['skills']); ?></p>
                    <p><strong>Experience:</strong> <?php echo htmlspecialchars($profile['experience']); ?> years</p>
                    <?php if ($profile['resume_path']): ?>
                        <p><a href="uploads/<?php echo htmlspecialchars($profile['resume_path']); ?>" target="_blank">View Resume</a></p>
                    <?php endif; ?>
                    <?php if ($profile['video_path']): ?>
                        <p><a href="uploads/<?php echo htmlspecialchars($profile['video_path']); ?>" target="_blank">View Video Intro</a></p>
                    <?php endif; ?>
                <?php else: ?>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($profile['company_name']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($profile['company_description']); ?></p>
                    <?php if ($profile['logo_path']): ?>
                        <p><img src="uploads/<?php echo htmlspecialchars($profile['logo_path']); ?>" alt="Company Logo" style="max-width: 100px;"></p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
