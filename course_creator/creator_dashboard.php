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

// Fetch data for dashboard
$totalcourses = 0;
$totalcourses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses"))['total'];


?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Creator Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
include('navbar.php');
?>
<div class="container mt-5">
    <h2 class="text-center">Course Creator Dashboard</h2>
    <div class="row mt-4">
        <!-- Total Courses Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <p class="card-text"><?php echo $totalcourses; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
