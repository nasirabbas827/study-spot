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

// Function to fetch and display PDF files for a course
function displayPDFs($conn, $course_id, $user_id) {
    $query = "SELECT course_pdfs.*, COALESCE(progress.PDFProgress, 0) AS PDFProgress
              FROM course_pdfs
              LEFT JOIN course_progress AS progress
              ON course_pdfs.PdfID = progress.PDFID
              AND progress.UserID = ?
              WHERE course_pdfs.CourseID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>PDF Files for Course</h2>";
        while ($row = mysqli_fetch_assoc($result)) {
            $pdfID = $row['PdfID'];
            $pdfName = $row['PdfName'];
            $pdfPath = $row['PdfPath'];
            $pdfProgress = $row['PDFProgress'];

            echo "<a href='download_pdf.php?pdfID=$pdfID' target='_blank'>$pdfName</a>";
            echo " - Progress: $pdfProgress%<br>";
        }
    } else {
        echo "No PDF files available for this course.";
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>PDF Files</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <?php
        displayPDFs($conn, $course_id, $user_id);
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
// Close the database connection
mysqli_close($conn);
?>
