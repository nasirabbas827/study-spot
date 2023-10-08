<?php
include('config.php');

session_start();
// Fetch published courses from the database
$publishedCourses = array();
$query = "SELECT CourseID, CourseName, CourseDescription, CourseObjectives, CoursePicture FROM courses WHERE PublishStatus = 'Published'";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $publishedCourses[] = $row;
    }
}

// Fetch counts for total courses, total instructors, total users, and total enrolled users
$totalCourses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses"))['total'];
$totalInstructors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM instructors"))['total'];
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalEnrolledUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM enroll_courses"))['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Study Spot</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <style>
    /* Add custom styles for the background image */
    .jumbotron {
        background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.5)), url('https://images.pexels.com/photos/2908984/pexels-photo-2908984.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        color: white; /* Change text color for better visibility */
        height: 500px; /* Adjust the height as needed */
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    /* Customize the text style within the jumbotron */
    .jumbotron h1,
    .jumbotron .lead,
    .jumbotron .btn {
        color: white;
    }

    /* Adjust the spacing for the jumbotron content */
    .jumbotron .lead {
        margin-top: 20px;
    }

    .jumbotron .btn {
        margin-top: 30px;
    }

    </style>
</head>
<body>

<?php
include('navbar.php');
?>

<div class="jumbotron jumbotron-fluid">
    <div class="container text-center">
        <h1 class="display-4">Explore Online Courses</h1>
        <p class="lead">
            <span class="typewriter">Discover a wide range of educational courses for your learning journey.</span>
        </p>
        <a href="login.php" class="btn btn-primary btn-lg">Browse Courses</a>
    </div>
</div>



<div class="container mt-5">
    <h3>Published Courses</h3>
    <div class="row">
        <?php foreach ($publishedCourses as $course) : ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="./admin/uploads/<?php echo $course['CoursePicture']; ?>" class="card-img-top" alt="Course Image">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $course['CourseName']; ?></h5>
                        <p class="card-text"><?php echo $course['CourseDescription']; ?></p>
                        <p class="card-text"><strong>Course Objectives:</strong></p>
                        <p class="card-text"><?php echo $course['CourseObjectives']; ?></p>
                         <a href="login.php" class="btn btn-primary">Enroll Now</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Display Beautiful Cards with Counts -->
<div class="container mt-3">
    <div class="row">
        <!-- Total Courses Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <p class="card-text"><?php echo $totalCourses; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Instructors Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-success">
                <div class="card-body">
                    <h5 class="card-title">Total Instructors</h5>
                    <p class="card-text"><?php echo $totalInstructors; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-info">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Enrolled Users Card -->
        <div class="col-md-3 mb-4">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Enrolled Users</h5>
                    <p class="card-text"><?php echo $totalEnrolledUsers; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Display Instructors as Cards -->
<div class="container mt-5">
    <h3>Instructors</h3>
    <div class="row">
        <?php
        $instructorQuery = "SELECT * FROM instructors";
        $instructorResult = mysqli_query($conn, $instructorQuery);

        while ($instructor = mysqli_fetch_assoc($instructorResult)) :
        ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $instructor['instructor_name']; ?></h5>
                        <p class="card-text"><strong>Instructor ID:</strong> <?php echo $instructor['instructor_id']; ?></p>
                        <p class="card-text"><strong>Bio:</strong> <?php echo $instructor['instructor_bio']; ?></p>
                        <p class="card-text"><strong>Contact Information:</strong> <?php echo $instructor['contact_information']; ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="mt-5 py-3 bg-light">
    <div class="container text-center">
        <p>&copy; 2023 Study Spot. All rights reserved.</p>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
