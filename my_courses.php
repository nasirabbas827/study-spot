<?php
include('config.php');
session_start();

// Check if the user is logged in, if not, redirect to the login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Function to fetch enrolled courses for the user
function getEnrolledCourses($conn, $user_id) {
    $query = "SELECT c.*, i.instructor_name
              FROM courses c
              INNER JOIN instructors i ON c.InstructorID = i.instructor_id
              INNER JOIN enroll_courses e ON c.CourseID = e.CourseID
              WHERE e.UserID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result;
}

$enrolledCourses = getEnrolledCourses($conn, $user_id);

if ($enrolledCourses) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>My Courses</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include('navbar.php'); ?>

        <div class="container mt-5">
        <h2>My Enrolled Courses</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($enrolledCourses)) { ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="./admin/uploads/<?php echo $row["CoursePicture"]; ?>" class="card-img-top" alt="Course Picture" height="200">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row["CourseName"]; ?></h5>
                            <p class="card-text"><?php echo $row["CourseDescription"]; ?></p>
                            <p class="card-text"><strong>Instructor:</strong> <?php echo $row["instructor_name"]; ?></p>
                            <a href="view_content.php?courseID=<?php echo $row["CourseID"]; ?>" class="btn btn-primary">View Content</a>
                            <a href="cancel_enrollment.php?courseID=<?php echo $row["CourseID"]; ?>" class="btn btn-danger">Cancel Enrollment</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

        <!-- Bootstrap JS -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
} else {
    echo "Error fetching enrolled courses: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
