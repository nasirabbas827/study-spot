<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}
$uploadsFolder = "uploads/";

// Query to fetch all instructors
$instructorQuery = "SELECT instructor_id, instructor_name FROM instructors";
$instructorResult = $conn->query($instructorQuery);

// Handle form submission to create a new course
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseName = $_POST["courseName"];
    $courseDescription = $_POST["courseDescription"];
    $courseObjectives = $_POST["courseObjectives"];
    $publishStatus = $_POST["publishStatus"];
    $instructorID = $_POST["instructorID"]; // Selected instructor ID

    // Handle course picture upload
    $coursePicture = ""; // Initialize the variable to store the picture name

    if (!empty($_FILES['coursePicture']['name'])) {
        // ... (existing code for handling picture upload)
    }

    // Insert course data into the database
    $insertQuery = "INSERT INTO courses (CoursePicture, CourseName, CourseDescription, CourseObjectives, PublishStatus, InstructorID)
                    VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "ssssss", $coursePicture, $courseName, $courseDescription, $courseObjectives, $publishStatus, $instructorID);

    if (mysqli_stmt_execute($stmt)) {
        echo "Course created successfully!";
    } else {
        echo "Error creating course: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Course</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('admin_navbar.php');
?>
<div class="container mt-5 mb-4">
    <h2>Create Course</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="coursePicture">Course Picture:</label>
                    <input type="file" class="form-control-file" name="coursePicture" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="courseName">Course Name:</label>
                    <input type="text" class="form-control" name="courseName" required>
                </div>
                <div class="form-group">
                    <label for="courseDescription">Course Description:</label>
                    <textarea class="form-control" name="courseDescription"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="courseObjectives">Course Objectives:</label>
                    <textarea class="form-control" name="courseObjectives"></textarea>
                </div>
                <div class="form-group">
                    <label for="publishStatus">Publish Status:</label>
                    <select class="form-control" name="publishStatus">
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="instructorID">Select Instructor:</label>
                    <select class="form-control" name="instructorID">
                        <?php
                        while ($instructorRow = $instructorResult->fetch_assoc()) {
                            echo "<option value='" . $instructorRow['instructor_id'] . "'>" . $instructorRow['instructor_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Create Course">
            <a href="view_courses.php" class="btn btn-secondary">View Courses</a>
        </div>
    </form>
</div>

<!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
