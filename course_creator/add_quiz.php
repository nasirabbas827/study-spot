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
// Handle form submission to add a quiz
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseID = $_POST["courseID"];
    $question = $_POST["question"];
    $option1 = $_POST["option1"];
    $option2 = $_POST["option2"];
    $option3 = $_POST["option3"];
    $option4 = $_POST["option4"];
    $correctAnswer = $_POST["correctAnswer"];

    // Insert quiz data into the database
    $insertQuery = "INSERT INTO quizzes (CourseID, Question, Option1, Option2, Option3, Option4, CorrectAnswer)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "issssss", $courseID, $question, $option1, $option2, $option3, $option4, $correctAnswer);

    if (mysqli_stmt_execute($stmt)) {
        echo "Quiz added successfully!";
    } else {
        echo "Error adding quiz: " . mysqli_error($conn);
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
    <title>Add Quiz</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('navbar.php');
?>
<div class="container mt-5 mb-4">
    <h2>Add Quiz</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="courseID">Course:</label>
                    <select class="form-control" name="courseID" required>
                        <?php
                        while ($course = mysqli_fetch_assoc($courseResult)) {
                            echo "<option value='" . $course["CourseID"] . "'>" . $course["CourseName"] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="question">Quiz Question:</label>
                    <input type="text" class="form-control" name="question" required>
                </div>
                <div class="form-group">
                    <label for="option1">Option 1:</label>
                    <input type="text" class="form-control" name="option1" required>
                </div>
                <div class="form-group">
                    <label for="option2">Option 2:</label>
                    <input type="text" class="form-control" name="option2" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="option3">Option 3:</label>
                    <input type="text" class="form-control" name="option3" required>
                </div>
                <div class="form-group">
                    <label for="option4">Option 4:</label>
                    <input type="text" class="form-control" name="option4" required>
                </div>
                <div class="form-group">
                    <label for="correctAnswer">Correct Answer:</label>
                    <input type="text" class="form-control" name="correctAnswer" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Add Quiz">
            <a href="view_quizzes.php" class="btn btn-dark mr-2">View Quizzes</a>
        </div>
    </form>
</div>
    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
