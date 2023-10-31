<?php
session_start();
include('config.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get values from the form
    $creatorName = $_POST["creator_name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password for security
    $contact = $_POST["contact"];
    $qualifications = $_POST["qualifications"];
    
    // Set the status to "pending"
    $status = "pending";
    
    // Perform validation (you can add more validation as needed)
    if (empty($creatorName) || empty($email) || empty($password) || empty($contact) || empty($qualifications)) {
        echo "All fields are required.";
    } else {

        // Insert data into the database
        $sql = "INSERT INTO course_creators (creator_name, email, password, contact, qualifications, status) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // Bind parameters and execute the query
            mysqli_stmt_bind_param($stmt, "ssssss", $creatorName, $email, $password, $contact, $qualifications, $status);
            if (mysqli_stmt_execute($stmt)) {
                echo "You are Registered Successfully Status is Pending Wait for Admin Approval";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        // Close the database connection
        mysqli_close($conn);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Create Course Creator</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php
include('navbar.php');
?>

<div class="container mt-4">
<h2>Create Course Creator</h2>
<form method="POST" action="">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="creator_name">Creator Name:</label>
                <input type="text" class="form-control" id="creator_name" name="creator_name" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" class="form-control" id="contact" name="contact" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="qualifications">Qualifications:</label>
                <textarea class="form-control" id="qualifications" name="qualifications" rows="4" required></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </div>
</form>

</div>

<!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
