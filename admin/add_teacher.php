<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}


// Initialize variables to store form data
$instructor_name = $instructor_bio = $contact_information = "";
$success_message = $error_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get data from the form
    $instructor_name = $_POST['instructor_name'];
    $instructor_bio = $_POST['instructor_bio'];
    $contact_information = $_POST['contact_information'];

    // Perform data validation (you can add more validation)
    if (empty($instructor_name) || empty($instructor_bio) || empty($contact_information)) {
        $error_message = "All fields are required.";
    } else {
        // SQL query to insert data into the instructors table
        $sql = "INSERT INTO instructors (instructor_name, instructor_bio, contact_information)
                VALUES ('$instructor_name', '$instructor_bio', '$contact_information')";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Instructor added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close the database connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Instructor</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('admin_navbar.php');
?>
    <div class="container mt-4">
        <h1>Add Instructor</h1>
        <?php
        // Display success or error messages
        if (!empty($success_message)) {
            echo '<p style="color: green;">' . $success_message . '</p>';
        } elseif (!empty($error_message)) {
            echo '<p style="color: red;">' . $error_message . '</p>';
        }
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="form-group">
                <label for="instructor_name">Instructor Name:</label>
                <input type="text" class="form-control" name="instructor_name" required>
            </div>

            <div class="form-group">
                <label for="instructor_bio">Instructor Bio:</label>
                <textarea class="form-control" name="instructor_bio" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="contact_information">Contact Information:</label>
                <input type="number" class="form-control" name="contact_information" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Instructor</button>
            <a href="view_teacher.php" class="btn btn-secondary">View Teachers</a>
        </form>
    </div>

    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
