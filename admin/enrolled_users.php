<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch the list of enrolled users with their course details, course picture, and instructor name
$enrolledUsers = array();
$query = "SELECT u.username, c.CourseName, ec.EnrollmentID, ec.EnrollmentDate, c.CoursePicture, i.instructor_name
          FROM users u
          INNER JOIN enroll_courses ec ON u.id = ec.UserID
          INNER JOIN courses c ON ec.CourseID = c.CourseID
          INNER JOIN instructors i ON c.InstructorID = i.instructor_id";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $enrolledUsers[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Enrolled Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2>Enrolled Users</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Enroll ID</th>
                    <th>Username</th>
                    <th>Course Name</th>
                    <th>Course Picture</th>
                    <th>Instructor Name</th>
                    <th>Enrollment Date</th>
                    <th>View Student Progress</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php foreach ($enrolledUsers as $user) : ?>
                    <tr>
                        <td><?php echo $user['EnrollmentID']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['CourseName']; ?></td>
                        <td><img src="../admin/uploads/<?php echo $user['CoursePicture']; ?>" alt="Course Picture" width="100"></td>
                        <td><?php echo $user['instructor_name']; ?></td>
                        <td><?php echo $user['EnrollmentDate']; ?></td>
                        <td><a href="view_student_progress.php?enrollmentID=<?php echo $user['EnrollmentID']; ?>" class="btn btn-primary">View Progress</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
