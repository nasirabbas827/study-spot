<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to delete a quiz
function deleteQuiz($conn, $quizID) {
    $deleteQuery = "DELETE FROM quizzes WHERE QuizID = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $quizID);
    
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Handle delete action
if (isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $quizID = $_GET["delete"];
    
    if (deleteQuiz($conn, $quizID)) {
        echo "Quiz deleted successfully!";
    } else {
        echo "Error deleting quiz: " . mysqli_error($conn);
    }
}

// Query the database to fetch a list of quizzes
$query = "SELECT * FROM quizzes";
$result = mysqli_query($conn, $query);

if ($result) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin Quizzes</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
    </head>
    <body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2>Admin Quizzes</h2>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>Quiz ID</th>
                    <th>Course ID</th>
                    <th>Question</th>
                    <th>Options</th>
                    <th>Correct Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row["QuizID"]; ?></td>
                        <td><?php echo $row["CourseID"]; ?></td>
                        <td><?php echo $row["Question"]; ?></td>
                        <td>
                            <?php
                            echo "1. " . $row["Option1"] . "<br>";
                            echo "2. " . $row["Option2"] . "<br>";
                            echo "3. " . $row["Option3"] . "<br>";
                            echo "4. " . $row["Option4"];
                            ?>
                        </td>
                        <td><?php echo $row["CorrectAnswer"]; ?></td>
                        <td>
                            <a href="edit_quiz.php?quizID=<?php echo $row["QuizID"]; ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?php echo $row["QuizID"]; ?>" onclick="return confirm('Are you sure you want to delete this quiz?')" class="mt-2 btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php
} else {
    echo "Error fetching quizzes: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
