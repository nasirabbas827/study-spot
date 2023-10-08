<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Query to fetch all instructors
$sql = "SELECT * FROM instructors";
$result = $conn->query($sql);

// Check if the form is submitted for deleting an instructor
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_instructor"])) {
    $instructor_id_to_delete = $_POST["delete_instructor"];

    // SQL query to delete the instructor
    $delete_sql = "DELETE FROM instructors WHERE instructor_id = $instructor_id_to_delete";

    if ($conn->query($delete_sql) === TRUE) {
        // Redirect to this page after successful deletion
        header("Location: view_teacher.php");
        exit;
    } else {
        echo "Error deleting instructor: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Instructors</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-4">
        <h1>View Instructors</h1>
        <a href="add_teacher.php" class="btn btn-primary mb-4 float-right">Add Instructors</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Instructor Name</th>
                    <th>Instructor Bio</th>
                    <th>Contact Information</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['instructor_name'] . "</td>";
                    echo "<td>" . $row['instructor_bio'] . "</td>";
                    echo "<td>" . $row['contact_information'] . "</td>";
                    echo "<td>
                            <a href='edit_teacher.php?id=" . $row['instructor_id'] . "' class='btn btn-primary'>Edit</a> | 
                            <form method='post' style='display: inline;'>
                                <input type='hidden' name='delete_instructor' value='" . $row['instructor_id'] . "'>
                                <button type='submit' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this instructor?\")'>Delete</button>
                            </form>
                        </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
