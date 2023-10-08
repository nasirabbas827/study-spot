<?php
session_start();
include('config.php');

// Check if the user is logged in as a creator
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "creator") {
    header("Location: creator_login.php"); // Redirect to the creator login page if not logged in
    exit;
}

// Get the creator's information from the session
$creatorId = $_SESSION["creator_id"];
$creatorName = $_SESSION["creator_name"];

// Function to delete a lecture
function deleteLecture($conn, $lectureID) {
    $deleteQuery = "DELETE FROM lectures WHERE LectureID = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $lectureID);
    
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Handle delete action
if (isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $lectureID = $_GET["delete"];
    
    if (deleteLecture($conn, $lectureID)) {
        echo "Lecture deleted successfully!";
    } else {
        echo "Error deleting lecture: " . mysqli_error($conn);
    }
}

// Query the database to fetch a list of lectures
$query = "SELECT lectures.*, courses.CourseName 
          FROM lectures 
          INNER JOIN courses ON lectures.CourseID = courses.CourseID";
$result = mysqli_query($conn, $query);

if ($result) {
    ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Lectures</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Admin Lectures</h2>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Lecture ID</th>
                    <th>Course Name</th>
                    <th>Lecture Title</th>
                    <th>YouTube URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row["LectureID"]; ?></td>
                        <td><?php echo $row["CourseName"]; ?></td>
                        <td><?php echo $row["LectureTitle"]; ?></td>
                        <td><?php echo $row["YoutubeURL"]; ?></td>
                        <td>
                            <a href="edit_lecture.php?lectureID=<?php echo $row["LectureID"]; ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?php echo $row["LectureID"]; ?>" onclick="return confirm('Are you sure you want to delete this lecture?')" class="mt-2 btn btn-danger">Delete</a>
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
    echo "Error fetching lectures: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
