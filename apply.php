<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'candidate') {
    echo "Please login as a candidate.";
    exit;
}
$job_id = $_POST['job_id'];
$candidate_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("INSERT INTO applications (job_id, candidate_id) VALUES (?, ?)");
if ($stmt->execute([$job_id, $candidate_id])) {
    echo "Application submitted successfully!";
} else {
    echo "Application failed.";
}
?>
