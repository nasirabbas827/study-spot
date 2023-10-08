<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    $creatorId = $_GET['id'];

    // Fetch the course creator data from the database
    $sql = "SELECT * FROM course_creators WHERE creator_id = $creatorId";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $creatorName = $row['creator_name'];
        $email = $row['email'];
        $contact = $row['contact'];
        $qualifications = $row['qualifications'];
    } else {
        echo "Course creator not found.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Handle form submission to update course creator data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newCreatorName = $_POST["creator_name"];
    $newEmail = $_POST["email"];
    $newContact = $_POST["contact"];
    $newQualifications = $_POST["qualifications"];

    // Update the course creator data in the database
    $updateSql = "UPDATE course_creators SET creator_name = ?, email = ?, contact = ?, qualifications = ? WHERE creator_id = ?";
    $stmt = mysqli_prepare($conn, $updateSql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssi", $newCreatorName, $newEmail, $newContact, $newQualifications, $creatorId);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "Course creator updated successfully.";
        } else {
            echo "Error updating course creator: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course Creator</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('admin_navbar.php');
?>

<div class="container mt-4">
    <h2>Edit Course Creator</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="creator_name">Creator Name:</label>
            <input type="text" class="form-control" id="creator_name" name="creator_name" value="<?php echo $creatorName; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
        </div>
        <div class="form-group">
            <label for="contact">Contact:</label>
            <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $contact; ?>" required>
        </div>
        <div class="form-group">
            <label for="qualifications">Qualifications:</label>
            <textarea class="form-control" id="qualifications" name="qualifications" rows="4" required><?php echo $qualifications; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
