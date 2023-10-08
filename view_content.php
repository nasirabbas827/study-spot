<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Get the courseID from the query string
if (isset($_GET["courseID"])) {
    $course_id = $_GET["courseID"];
} else {
    echo "Invalid course ID.";
    exit;
}

// Function to fetch and display the progress of each course content type
function displayContentProgress($conn, $user_id, $course_id) {
    // Fetch and calculate progress for Video Lectures
    $lecturesQuery = "SELECT COALESCE(SUM(progress.LectureProgress), 0) AS LectureProgress
                     FROM lectures
                     LEFT JOIN course_progress AS progress
                     ON lectures.LectureID = progress.LectureID
                     AND progress.UserID = ?
                     WHERE lectures.CourseID = ?";
    $lecturesStmt = mysqli_prepare($conn, $lecturesQuery);
    mysqli_stmt_bind_param($lecturesStmt, "ii", $user_id, $course_id);
    mysqli_stmt_execute($lecturesStmt);
    $lecturesResult = mysqli_stmt_get_result($lecturesStmt);
    $lecturesRow = mysqli_fetch_assoc($lecturesResult);
    $lectureProgress = $lecturesRow["LectureProgress"];

    
    // Fetch and calculate progress for PDF Files
    $pdfsQuery = "SELECT COALESCE(SUM(progress.PDFProgress), 0) AS PDFProgress
                  FROM course_pdfs
                  LEFT JOIN course_progress AS progress
                  ON course_pdfs.PdfID = progress.PDFID
                  AND progress.UserID = ?
                  WHERE course_pdfs.CourseID = ?";
    $pdfsStmt = mysqli_prepare($conn, $pdfsQuery);
    mysqli_stmt_bind_param($pdfsStmt, "ii", $user_id, $course_id);
    mysqli_stmt_execute($pdfsStmt);
    $pdfsResult = mysqli_stmt_get_result($pdfsStmt);
    $pdfsRow = mysqli_fetch_assoc($pdfsResult);
    $pdfProgress = $pdfsRow["PDFProgress"];

    // Display the progress for each content type
    echo "<h3>Course Progress</h3>";
    echo "<p>Video Lectures Progress: $lectureProgress%</p>";
    echo "<p>PDF Files Progress: $pdfProgress%</p>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Course Content</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Course Content</h2>
        <!-- Display the progress for each content type -->
        <?php
        displayContentProgress($conn, $user_id, $course_id);
        ?>

        <!-- Add buttons for Video Lectures, Quizzes, and PDF Files -->
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openLectures()">Video Lectures</button>
            <button class="btn btn-success" onclick="openQuizzes()">Quizzes</button>
            <button class="btn btn-info" onclick="openPDFs()">PDF Files</button>
        </div>

        <!-- JavaScript to open corresponding pages -->
        <script>
            function openLectures() {
                window.location.href = 'lectures.php?courseID=<?php echo $course_id; ?>';
            }

            function openQuizzes() {
                window.location.href = 'quizzes.php?courseID=<?php echo $course_id; ?>';
            }

            function openPDFs() {
                window.location.href = 'pdfs.php?courseID=<?php echo $course_id; ?>';
            }
        </script>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
