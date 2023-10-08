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

// Check if the pdfID is provided via GET
if (isset($_GET["pdfID"])) {
    $pdf_id = $_GET["pdfID"];
} else {
    echo "Invalid PDF ID.";
    exit;
}

// Check if the PDF exists and belongs to the specified course
$query = "SELECT CourseID, PdfPath FROM course_pdfs WHERE PdfID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $pdf_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Error: PDF not found.";
    exit;
}

$row = mysqli_fetch_assoc($result);
$course_id = $row["CourseID"];
$pdfPath = $row["PdfPath"];

// Update the progress for this PDF in the progress table
$update_query = "INSERT INTO course_progress (UserID, CourseID, PDFID, PDFProgress)
                 VALUES (?, ?, ?, 100)
                 ON DUPLICATE KEY UPDATE PDFProgress = 100";
$stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $course_id, $pdf_id);

if (mysqli_stmt_execute($stmt)) {
    // Success! PDF marked as viewed.
    
    // Force download the PDF file
    $file_path = "./admin/" . $pdfPath;
    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=" . basename($file_path));
    header("Content-Length: " . filesize($file_path));
    readfile($file_path);
} else {
    echo "Error: Failed to mark PDF as viewed.";
}

// Close the database connection
mysqli_close($conn);
?>
