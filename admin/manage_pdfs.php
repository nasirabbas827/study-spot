<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to delete a PDF
function deletePDF($conn, $pdfID) {
    $deleteQuery = "DELETE FROM course_pdfs WHERE PdfID = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $pdfID);

    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Handle delete action
if (isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $pdfID = $_GET["delete"];

    if (deletePDF($conn, $pdfID)) {
        echo "PDF deleted successfully!";
    } else {
        echo "Error deleting PDF: " . mysqli_error($conn);
    }
}

// Query the database to fetch a list of PDFs
$query = "SELECT course_pdfs.PdfID, courses.CourseName, course_pdfs.PdfName, course_pdfs.PdfPath
          FROM course_pdfs
          INNER JOIN courses ON course_pdfs.CourseID = courses.CourseID";
$result = mysqli_query($conn, $query);

if ($result) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Manage PDFs</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5 mb-4">
        <h2>Manage PDFs</h2>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Course Name</th>
                    <th>PDF Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row["CourseName"]; ?></td>
                        <td><?php echo $row["PdfName"]; ?></td>
                        <td>
                            <a href="<?php echo $row["PdfPath"]; ?>" target="_blank" class="btn btn-primary">View</a>
                            <a href="?delete=<?php echo $row["PdfID"]; ?>" onclick="return confirm('Are you sure you want to delete this PDF?')" class="mt-2 btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
} else {
    echo "Error fetching PDFs: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
