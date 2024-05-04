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

    // Fetch problem details based on problemID
    $stmt = $pdo->prepare('SELECT problemname, link FROM problem WHERE problemID = ?');
    $stmt->execute([$problemID]);
    $problemDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Display a form for editing the problem
    if ($problemDetails) {
        // Form for editing the problem
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Problem</title>
        </head>
        <body>
            <h2>Edit Problem</h2>
            <form action="update_problem.php" method="post">
                <input type="hidden" name="problemID" value="<?= $problemID ?>">
                <label for="editProblemName">Problem Name:</label>
                <input type="text" name="editProblemName" value="<?= htmlspecialchars($problemDetails['problemname']) ?>" required>
                <br>
                <label for="editProblemLink">Problem Link:</label>
                <input type="text" name="editProblemLink" value="<?= htmlspecialchars($problemDetails['link']) ?>" required>
                <br>
                <input type="submit" value="Update Problem">
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Problem not found.";
    }
} else {
    echo "Invalid request.";
}
?>
