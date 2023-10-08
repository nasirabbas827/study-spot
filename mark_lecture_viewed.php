<?php
include('config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    echo "Error: User is not logged in.";
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Check if the lectureID is provided via POST
if (isset($_POST["lectureID"])) {
    $lecture_id = $_POST["lectureID"];
} else {
    echo "Error: Lecture ID is missing.";
    exit;
}

// Check if the lecture exists and belongs to the specified course
$query = "SELECT CourseID FROM lectures WHERE LectureID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $lecture_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Error: Lecture not found.";
    exit;
}

$row = mysqli_fetch_assoc($result);
$course_id = $row["CourseID"];

// Update the progress for this lecture in the course_progress table
$update_query = "INSERT INTO course_progress (UserID, CourseID, LectureID, LectureProgress)
                 VALUES (?, ?, ?, 100)
                 ON DUPLICATE KEY UPDATE LectureProgress = 100";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $course_id, $lecture_id);

if (mysqli_stmt_execute($stmt)) {
    // Success! Lecture marked as viewed.
    echo "success";
} else {
    echo "Error: Failed to mark lecture as viewed.";
}

// Close the database connection
mysqli_close($conn);
?>
