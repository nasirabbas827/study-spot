<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Get the enrollment ID from the query string
if (isset($_GET["enrollmentID"])) {
    $enrollmentID = $_GET["enrollmentID"];
} else {
    echo "Invalid enrollment ID.";
    exit;
}

// Fetch the enrolled user's ID from the enroll_courses table
$query = "SELECT UserID, CourseID FROM enroll_courses WHERE EnrollmentID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $enrollmentID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $enrolledUserID = $row['UserID'];
    $courseID = $row['CourseID'];
} else {
    echo "Enrollment not found.";
    exit;
}

// Fetch the student's name and course name
$query = "SELECT u.username, c.CourseName
          FROM users u
          INNER JOIN courses c ON c.CourseID = ?
          WHERE u.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $courseID, $enrolledUserID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $studentName = $row['username'];
    $courseName = $row['CourseName'];
} else {
    echo "Student or Course not found.";
    exit;
}

// Fetch the student's quiz results (TotalScore, TotalQuizzesAttempted, timestamp)
$quizResults = array();
$query = "SELECT TotalScore, TotalQuizzesAttempted, timestamp
          FROM quiz_results
          WHERE UserID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $enrolledUserID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $quizResults[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Progress</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2>Student Progress</h2>
        <h3>Student Name: <?php echo $studentName; ?></h3>
        <h3>Course Name: <?php echo $courseName; ?></h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Total Score</th>
                    <th>Total Quizzes Attempted</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizResults as $result) : ?>
                    <tr>
                        <td><?php echo $result['TotalScore']; ?></td>
                        <td><?php echo $result['TotalQuizzesAttempted']; ?></td>
                        <td><?php echo $result['timestamp']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
