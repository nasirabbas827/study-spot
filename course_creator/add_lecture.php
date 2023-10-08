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

// Handle form submission to add a new lecture
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseID = $_POST["courseID"];
    $lectureTitle = $_POST["lectureTitle"];
    $youtubeURL = $_POST["youtubeURL"];

    // Insert lecture data into the database
    $insertQuery = "INSERT INTO lectures (CourseID, LectureTitle, YoutubeURL)
                    VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "iss", $courseID, $lectureTitle, $youtubeURL);

    if (mysqli_stmt_execute($stmt)) {
        echo "Lecture added successfully!";
    } else {
        echo "Error adding lecture: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
}

// Fetch the list of courses to populate the dropdown
$courseQuery = "SELECT * FROM courses";
$courseResult = mysqli_query($conn, $courseQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Lecture</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('navbar.php');
?>
<div class="container mt-5 mb-4">
    <h2>Add Lecture</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="courseID">Select Course:</label>
            <select class="form-control" name="courseID" required>
                <option value="">Select a Course</option>
                <?php
                while ($course = mysqli_fetch_assoc($courseResult)) {
                    echo "<option value='" . $course["CourseID"] . "'>" . $course["CourseName"] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="lectureTitle">Lecture Title:</label>
            <input type="text" class="form-control" name="lectureTitle" required>
        </div>
        <div class="form-group">
            <label for="youtubeURL">YouTube Video URL:</label>
            <input type="url" class="form-control" name="youtubeURL" placeholder="https://www.youtube.com/watch?v=your_video_id" required>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Add Lecture">
            <a href="view_lectures.php" class="btn btn-dark mr-2">View Lectures</a>
        </div>
    </form>
</div>
<!-- Bootstrap JS -->
<!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
