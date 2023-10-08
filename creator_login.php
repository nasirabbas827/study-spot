<?php
session_start();
include('config.php');

// Check if the user is already logged in
if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] === "creator") {
    header("Location: course_creator/creator_dashboard.php"); // Redirect to the creator's dashboard
    exit;
}

// Initialize variables for error messages
$error = "";

// Handle form submission for login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare a SQL statement to retrieve the user's data
    $sql = "SELECT * FROM course_creators WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Authentication successful
                $_SESSION["usertype"] = "creator";
                $_SESSION["creator_id"] = $row['creator_id'];
                $_SESSION["creator_name"] = $row['creator_name'];

                header("Location: course_creator/creator_dashboard.php"); // Redirect to the creator's dashboard
                exit;
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Email not found";
        }

        mysqli_stmt_close($stmt);
    } else {
        $error = "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Creator Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
    <h2>Course Creator Login</h2>
    <?php
    if (!empty($error)) {
        echo '<div class="alert alert-danger">' . $error . '</div>';
    }
    ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<!-- Add these links to the <head> section of your HTML file -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
