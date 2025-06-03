<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>redirectTo('login.php');</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['schedule'])) {
    $job_id = $_POST['job_id'];
    $candidate_id = $_POST['candidate_id'];
    $interview_date = $_POST['interview_date'];
    $interview_type = $_POST['interview_type'];
    $stmt = $pdo->prepare("INSERT INTO interviews (job_id, candidate_id, recruiter_id, interview_date, interview_type) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$job_id, $candidate_id, $user_id, $interview_date, $interview_type]);
    echo "<script>alert('Interview scheduled successfully!');</script>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $receiver_id = $_POST['receiver_id'];
    $job_id = $_POST['job_id'];
    $message = $_POST['message'];
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, job_id, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $job_id, $message]);
    echo "<script>alert('Message sent successfully!');</script>";
}

$interviews = $pdo->query("SELECT i.*, j.title, u.email FROM interviews i JOIN jobs j ON i.job_id = j.id JOIN users u ON i.candidate_id = u.id WHERE i.recruiter_id = $user_id OR i.candidate_id = $user_id")->fetchAll();
$messages = $pdo->query("SELECT m.*, u.email FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.receiver_id = $user_id OR m.sender_id = $user_id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interviews & Messages - Hiring Cafe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f0f4f8, #d9e2ec); }
        .container { max-width: 800px; margin: 50px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #2c3e50; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #2980b9; }
        .section { margin: 20px 0; }
        .interview-card, .message-card { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 10px; }
        @media (max-width: 768px) { .container { margin: 20px; } }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($user_type == 'recruiter'): ?>
            <div class="section">
                <h2>Schedule Interview</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="job_id">Job</label>
                        <select id="job_id" name="job_id" required>
                            <?php
                            $jobs = $pdo->query("SELECT * FROM jobs WHERE recruiter_id = $user_id")->fetchAll();
                            foreach ($jobs as $job):
                            ?>
                                <option value="<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="candidate_id">Candidate</label>
                        <select id="candidate_id" name="candidate_id" required>
                            <?php
                            $candidates = $pdo->query("SELECT u.id, c.full_name FROM users u JOIN candidate_profiles c ON u.id = c.user_id WHERE u.user_type = 'candidate'")->fetchAll();
                            foreach ($candidates as $candidate):
                            ?>
                                <option value="<?php echo $candidate['id']; ?>"><?php echo htmlspecialchars($candidate['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="interview_date">Interview Date</label>
                        <input type="datetime-local" id="interview_date" name="interview_date" required>
                    </div>
                    <div class="form-group">
                        <label for="interview_type">Interview Type</label>
                        <select id="interview_type" name="interview_type" required>
                            <option value="video">Video</option>
                            <option value="instant">Instant</option>
                        </select>
                    </div>
                    <button type="submit" name="schedule">Schedule</button>
                </form>
            </div>
        <?php endif; ?>
        <div class="section">
            <h2>Your Interviews</h2>
            <?php foreach ($interviews as $interview): ?>
                <div class="interview-card">
                    <p><strong>Job:</strong> <?php echo htmlspecialchars($interview['title']); ?></p>
                    <p><strong>Candidate:</strong> <?php echo htmlspecialchars($interview['email']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($interview['interview_date']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($interview['interview_type']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($interview['status']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="section">
            <h2>Messages</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="receiver_id">To</label>
                    <select id="receiver_id" name="receiver_id" required>
                        <?php
                        $users = $pdo->query("SELECT id, email FROM users WHERE id != $user_id")->fetchAll();
                        foreach ($users as $user):
                        ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['email']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="job_id">Job (Optional)</label>
                    <select id="job_id" name="job_id">
                        <option value="">Select Job</option>
                        <?php foreach ($jobs as $job): ?>
                            <option value="<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <button type="submit" name="message">Send Message</button>
            </form>
            <div class="section">
                <?php foreach ($messages as $message): ?>
                    <div class="message-card">
                        <p><strong>From:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                        <p><strong>Message:</strong> <?php echo htmlspecialchars($message['message']); ?></p>
                        <p><strong>Sent:</strong> <?php echo htmlspecialchars($message['sent_at']); ?></p>
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
