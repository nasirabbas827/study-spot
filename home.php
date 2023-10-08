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

// Function to fetch all instructors
function getAllInstructors($conn) {
    $query = "SELECT * FROM instructors";
    $result = mysqli_query($conn, $query);
    return $result;
}

// Fetch all instructors
$instructorsResult = getAllInstructors($conn);

// Function to check if a user is enrolled in a course
function isEnrolled($conn, $user_id, $course_id) {
    $checkQuery = "SELECT * FROM enroll_courses WHERE UserID = ? AND CourseID = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}

// Fetch and display courses with course picture and instructor name
$query = "SELECT c.*, i.instructor_name
          FROM courses c
          INNER JOIN instructors i ON c.InstructorID = i.instructor_id
          WHERE c.PublishStatus = 'Published'";
$result = mysqli_query($conn, $query);

if ($result) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Homepage</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <body>
        <?php include('navbar.php'); ?>

        <div class="container mt-5">
    <h2>Available Courses</h2>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="./admin/uploads/<?php echo $row["CoursePicture"]; ?>" class="card-img-top" alt="Course Picture">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row["CourseName"]; ?></h5>
                        <p class="card-text"><?php echo $row["CourseDescription"]; ?></p>
                        <p class="card-text"><strong>Instructor:</strong> <?php echo $row["instructor_name"]; ?></p>
                    </div>
                    <div class="card-footer">
                        <?php
                        if (isEnrolled($conn, $user_id, $row["CourseID"])) {
                            echo '<a href="view_content.php?courseID=' . $row["CourseID"] . '" class="btn btn-primary">View Content</a>';
                            echo ' <a href="cancel_enrollment.php?courseID=' . $row["CourseID"] . '" class="btn btn-danger">Cancel Enrollment</a>';
                        } else {
                            echo '<a href="enroll.php?courseID=' . $row["CourseID"] . '" class="btn btn-success">Enroll</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="container mt-5">
        <h2>Our Instructors</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($instructorsResult)) { ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row["instructor_name"]; ?></h5>
                            <p class="card-text"><strong>Bio:</strong> <?php echo $row["instructor_bio"]; ?></p>
                            <p class="card-text"><strong>Contact Information:</strong> <?php echo $row["contact_information"]; ?></p>
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
    echo "Error fetching courses: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
