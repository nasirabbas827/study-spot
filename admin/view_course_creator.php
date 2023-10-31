<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    $sql = "DELETE FROM course_creators WHERE creator_id = $deleteId";

    if (mysqli_query($conn, $sql)) {
        echo "Course creator deleted successfully.";
        header("Location: view_course_creator.php");
        exit();

    } else {
        echo "Error deleting course creator: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Course Creators</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('admin_navbar.php');
?>

<div class="container mt-4">
    <h2>Manage Course Creators</h2>
    <a class="mb-3 float-right btn btn-primary" href="add_course_creator.php">Add New Course Creator</a>
    <hr>
    <?php
    // Fetch and display course creators
    $sql = "SELECT * FROM course_creators";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo '<table class="table">';
        echo '<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Contact</th><th>Qualifications</th><th>Status</th><th>Action</th></tr></thead>';
        echo '<tbody>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['creator_id'] . '</td>';
            echo '<td>' . $row['creator_name'] . '</td>';
            echo '<td>' . $row['email'] . '</td>';
            echo '<td>' . $row['contact'] . '</td>';
            echo '<td>' . $row['qualifications'] . '</td>';
            echo '<td>' . $row['status'] . '</td>';
            echo '<td>';
            echo '<a class="btn btn-primary" href="edit_creator.php?id=' . $row['creator_id'] . '">Edit</a> | ';
            echo '<a class="btn btn-danger" href="?delete_id=' . $row['creator_id'] . '">Delete</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo 'No course creators found.';
    }

    mysqli_close($conn);
    ?>
</div>

<!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>