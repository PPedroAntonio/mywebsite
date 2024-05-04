<?php
// Include database connection code here
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'PROGTEAM';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['problemID'])) {
    $problemID = $_POST['problemID'];

    // Delete the problem from the database
    $stmt = $pdo->prepare('DELETE FROM problem WHERE problemID = ?');
    $stmt->execute([$problemID]);

    // Redirect back to the original page or a different page
    header('Location: index.php?categoryID=' . $categoryID);
    exit();
} else {
    echo "Invalid request.";
}
?>
