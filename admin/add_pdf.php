<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

$uploadsFolder = "pdf_uploads/";

// Handle form submission to add PDF to a course
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseID = $_POST["courseID"];
    $pdfName = $_FILES["pdfFile"]["name"];
    $pdfTmpName = $_FILES["pdfFile"]["tmp_name"];

    // Check if a file was uploaded
    if (!empty($pdfName) && !empty($pdfTmpName)) {
        // Move the uploaded PDF file to the specified folder
        $pdfDestination = $uploadsFolder . $pdfName;
        if (move_uploaded_file($pdfTmpName, $pdfDestination)) {
            // Insert PDF information into the database
            $insertQuery = "INSERT INTO course_pdfs (CourseID, PdfName, PdfPath) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($stmt, "iss", $courseID, $pdfName, $pdfDestination);

            if (mysqli_stmt_execute($stmt)) {
                echo "PDF added to the course successfully!";
            } else {
                echo "Error adding PDF to the course: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading PDF file.";
        }
    } else {
        echo "Please select a PDF file to upload.";
    }
}

// Query the database to fetch a list of courses
$query = "SELECT * FROM courses";
$result = mysqli_query($conn, $query);

if ($result) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Add PDF to Course</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5 mb-4">
        <h2>Add PDF to Course</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="courseID">Select Course:</label>
                <select class="form-control" name="courseID">
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <option value="<?php echo $row["CourseID"]; ?>"><?php echo $row["CourseName"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="pdfFile">Upload PDF:</label>
                <input type="file" class="form-control-file" name="pdfFile" accept=".pdf" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Add PDF">
                <a href="manage_pdfs.php" class="btn btn-dark mr-2">Manage PDFs</a>
            </div>
        </form>
    </div>
    <!-- Bootstrap JS -->
    <!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
} else {
    echo "Error fetching courses: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
