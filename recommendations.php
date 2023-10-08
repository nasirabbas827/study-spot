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

// Function to get the list of instructors whose courses the user is enrolled in
function getEnrolledInstructors($conn, $user_id) {
    $query = "SELECT DISTINCT i.instructor_id, i.instructor_name
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

// Get the list of instructors whose courses the user is enrolled in
$enrolledInstructors = getEnrolledInstructors($conn, $user_id);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recommendations</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Recommended Courses</h2>
        <div class="row">
            <?php 
            while ($instructor = mysqli_fetch_assoc($enrolledInstructors)) {
                $instructor_id = $instructor["instructor_id"];
                $enrolledCourseIDs = []; // Create an array to store course IDs user is enrolled in

                // Fetch course IDs that the user is already enrolled in for this instructor
                $enrolledCoursesQuery = "SELECT e.CourseID FROM enroll_courses e
                                        INNER JOIN courses c ON e.CourseID = c.CourseID
                                        WHERE e.UserID = ? AND c.InstructorID = ?";
                $stmt = mysqli_prepare($conn, $enrolledCoursesQuery);
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $instructor_id);
                mysqli_stmt_execute($stmt);
                $enrolledCoursesResult = mysqli_stmt_get_result($stmt);

                while ($enrolledCourse = mysqli_fetch_assoc($enrolledCoursesResult)) {
                    $enrolledCourseIDs[] = $enrolledCourse["CourseID"];
                }

                // Fetch and display recommended courses from this instructor that the user is not enrolled in
                $recommendedCoursesQuery = "SELECT c.* FROM courses c
                                            WHERE c.InstructorID = ? AND c.PublishStatus = 'Published'
                                            AND c.CourseID NOT IN (" . implode(",", $enrolledCourseIDs) . ")";
                $stmt = mysqli_prepare($conn, $recommendedCoursesQuery);
                mysqli_stmt_bind_param($stmt, "i", $instructor_id);
                mysqli_stmt_execute($stmt);
                $recommendedCourses = mysqli_stmt_get_result($stmt);

                while ($row = mysqli_fetch_assoc($recommendedCourses)) { ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="./admin/uploads/<?php echo $row["CoursePicture"]; ?>" class="card-img-top" alt="Course Picture">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row["CourseName"]; ?></h5>
                                <p class="card-text"><?php echo $row["CourseDescription"]; ?></p>
                                <p class="card-text"><strong>Instructor:</strong> <?php echo $instructor["instructor_name"]; ?></p>
                            </div>
                            <div class="card-footer">
                                <a href="enroll.php?courseID=<?php echo $row["CourseID"]; ?>" class="btn btn-success">Enroll</a>
                            </div>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
