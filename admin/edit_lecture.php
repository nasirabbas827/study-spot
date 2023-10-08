<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to update a lecture
function updateLecture($conn, $lectureID, $lectureTitle, $youtubeURL) {
    $updateQuery = "UPDATE lectures
                    SET LectureTitle = ?, YoutubeURL = ?
                    WHERE LectureID = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $lectureTitle, $youtubeURL, $lectureID);
    
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Handle form submission to update the lecture
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lectureID = $_POST["lectureID"];
    $lectureTitle = $_POST["lectureTitle"];
    $youtubeURL = $_POST["youtubeURL"];

    if (updateLecture($conn, $lectureID, $lectureTitle, $youtubeURL)) {
        // Redirect to view_lectures.php after successfully updating the lecture
        header("Location: view_lectures.php");
        exit;
    } else {
        echo "Error updating lecture: " . mysqli_error($conn);
    }
}

// Fetch lecture details for editing
if (isset($_GET["lectureID"]) && !empty($_GET["lectureID"])) {
    $lectureID = $_GET["lectureID"];
    $query = "SELECT * FROM lectures WHERE LectureID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $lectureID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $lectureTitle = $row["LectureTitle"];
        $youtubeURL = $row["YoutubeURL"];
    } else {
        echo "Lecture not found.";
        exit;
    }
} else {
    echo "Lecture ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Lecture</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('admin_navbar.php');
?>
<div class="container mt-5 mb-4">
    <h2>Edit Lecture</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="lectureID" value="<?php echo $lectureID; ?>">
        <div class="form-group">
            <label for="lectureTitle">Lecture Title:</label>
            <input type="text" class="form-control" name="lectureTitle" value="<?php echo $lectureTitle; ?>" required>
        </div>
        <div class="form-group">
            <label for="youtubeURL">YouTube Video URL:</label>
            <input type="text" class="form-control" name="youtubeURL" value="<?php echo $youtubeURL; ?>" required>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Update Lecture">
        </div>
    </form>
</div>
    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
