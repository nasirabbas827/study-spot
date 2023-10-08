<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET["id"])) {
    $instructor_id = $_GET["id"];

    // Query to fetch the instructor's details
    $sql = "SELECT * FROM instructors WHERE instructor_id = $instructor_id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Populate form fields with instructor details for editing
        $instructor_name = $row['instructor_name'];
        $instructor_bio = $row['instructor_bio'];
        $contact_information = $row['contact_information'];
    } else {
        echo "Instructor not found.";
        exit;
    }
} else {
    echo "Instructor ID not provided.";
    exit;
}

// Check if the form is submitted for editing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get updated data from the form
    $instructor_name = $_POST['instructor_name'];
    $instructor_bio = $_POST['instructor_bio'];
    $contact_information = $_POST['contact_information'];

    // Perform data validation (you can add more validation)
    if (empty($instructor_name) || empty($instructor_bio) || empty($contact_information)) {
        $error_message = "All fields are required.";
    } else {
        // SQL query to update instructor data
        $sql = "UPDATE instructors
                SET instructor_name = '$instructor_name',
                    instructor_bio = '$instructor_bio',
                    contact_information = '$contact_information'
                WHERE instructor_id = $instructor_id";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Instructor updated successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Instructor</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-4">
        <h1>Edit Instructor</h1>
        <?php
        // Display success or error messages
        if (!empty($success_message)) {
            echo '<p style="color: green;">' . $success_message . '</p>';
        } elseif (!empty($error_message)) {
            echo '<p style="color: red;">' . $error_message . '</p>';
        }
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] . '?id=' . $instructor_id; ?>" method="post">
            <div class="form-group">
                <label for="instructor_name">Instructor Name:</label>
                <input type="text" class="form-control" name="instructor_name" value="<?php echo $instructor_name; ?>" required>
            </div>

            <div class="form-group">
                <label for="instructor_bio">Instructor Bio:</label>
                <textarea class="form-control" name="instructor_bio" rows="4" required><?php echo $instructor_bio; ?></textarea>
            </div>

            <div class="form-group">
                <label for="contact_information">Contact Information:</label>
                <input type="text" class="form-control" name="contact_information" value="<?php echo $contact_information; ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Instructor</button>
        </form>
    </div>

    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
