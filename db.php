<?php
$host = "localhost";
$dbname = "dbmg6fhmiuohwg";
$username = "urnrgaote95vf";
$password = "tgk9ztof7xb1";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
