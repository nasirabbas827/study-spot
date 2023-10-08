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

// Get the courseID from the query string
if (isset($_GET["courseID"])) {
    $course_id = $_GET["courseID"];
} else {
    echo "Invalid course ID.";
    exit;
}

// Function to fetch and display lectures for a course
function displayLectures($conn, $course_id, $user_id) {
    $query = "SELECT lectures.*, COALESCE(progress.LectureProgress, 0) AS LectureProgress
              FROM lectures
              LEFT JOIN course_progress AS progress
              ON lectures.LectureID = progress.LectureID
              AND progress.UserID = ?
              WHERE lectures.CourseID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        echo "<h3>Video Lectures</h3>";
        while ($row = mysqli_fetch_assoc($result)) {
            $lectureID = $row["LectureID"];
            $lectureTitle = $row["LectureTitle"];
            $youtubeURL = $row["YoutubeURL"];
            $lectureProgress = $row["LectureProgress"];

            echo "<h4>Lecture Title: $lectureTitle</h4>";
            // Embed YouTube video using iframe
            echo '<iframe width="560" height="315" src="' . $youtubeURL . '" frameborder="0" allowfullscreen></iframe>';
            
            // Display progress bar
            echo "<div class='progress'>";
            echo "<div class='progress-bar' role='progressbar' style='width: $lectureProgress%;' aria-valuenow='$lectureProgress' aria-valuemin='0' aria-valuemax='100'></div>";
            echo "</div>";

            // Check if the user has already viewed the lecture
            if ($lectureProgress < 100) {
                // Display a "Viewed" button
                echo "<button class='btn btn-success' onclick='markAsViewed($lectureID)'>Viewed</button>";
            }
        }
    } else {
        echo "No video lectures available for this course.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Lectures</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <!-- JavaScript to handle the "Viewed" button -->
    <script>
    function markAsViewed(lectureID) {
        // Send an AJAX request to mark the lecture as viewed
        // Update the progress in the database
        // Reload or refresh the page to reflect the updated progress
        // You can use JavaScript libraries like jQuery for AJAX requests
        $.post('mark_lecture_viewed.php', { lectureID: lectureID }, function(data) {
            // Assuming 'mark_lecture_viewed.php' returns a response indicating success
            if (data === 'success') {
                // Reload the page to reflect the updated progress
                location.reload();
            } else {
                // Handle the error or display a message to the user
                alert('Failed to mark lecture as viewed. Please try again.');
            }
        });
    }
</script>

</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Video Lectures</h2>
        <?php
        displayLectures($conn, $course_id, $user_id);
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
mysqli_close($conn);
?>
