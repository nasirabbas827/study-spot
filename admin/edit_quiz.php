<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Function to update a quiz
function updateQuiz($conn, $quizID, $question, $option1, $option2, $option3, $option4, $correctAnswer) {
    $updateQuery = "UPDATE quizzes
                    SET Question = ?, Option1 = ?, Option2 = ?, Option3 = ?, Option4 = ?, CorrectAnswer = ?
                    WHERE QuizID = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssssssi", $question, $option1, $option2, $option3, $option4, $correctAnswer, $quizID);
    
    if (mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
}

// Handle form submission to update the quiz
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quizID = $_POST["quizID"];
    $question = $_POST["question"];
    $option1 = $_POST["option1"];
    $option2 = $_POST["option2"];
    $option3 = $_POST["option3"];
    $option4 = $_POST["option4"];
    $correctAnswer = $_POST["correctAnswer"];

    if (updateQuiz($conn, $quizID, $question, $option1, $option2, $option3, $option4, $correctAnswer)) {
        // Redirect to view_quizzes.php after successfully updating the quiz
        header("Location: view_quizzes.php");
        exit;
    } else {
        echo "Error updating quiz: " . mysqli_error($conn);
    }
}

// Fetch quiz details for editing
if (isset($_GET["quizID"]) && !empty($_GET["quizID"])) {
    $quizID = $_GET["quizID"];
    $query = "SELECT * FROM quizzes WHERE QuizID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $quizID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $question = $row["Question"];
        $option1 = $row["Option1"];
        $option2 = $row["Option2"];
        $option3 = $row["Option3"];
        $option4 = $row["Option4"];
        $correctAnswer = $row["CorrectAnswer"];
    } else {
        echo "Quiz not found.";
        exit;
    }
} else {
    echo "Quiz ID not provided.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php
include('admin_navbar.php');
?>
<div class="container mt-5 mb-4">
    <h2>Edit Quiz</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="quizID" value="<?php echo $quizID; ?>">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="question">Quiz Question:</label>
                    <input type="text" class="form-control" name="question" value="<?php echo $question; ?>" required>
                </div>
                <div class="form-group">
                    <label for="option1">Option 1:</label>
                    <input type="text" class="form-control" name="option1" value="<?php echo $option1; ?>" required>
                </div>
                <div class="form-group">
                    <label for="option2">Option 2:</label>
                    <input type="text" class="form-control" name="option2" value="<?php echo $option2; ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="option3">Option 3:</label>
                    <input type="text" class="form-control" name="option3" value="<?php echo $option3; ?>" required>
                </div>
                <div class="form-group">
                    <label for="option4">Option 4:</label>
                    <input type="text" class="form-control" name="option4" value="<?php echo $option4; ?>" required>
                </div>
                <div class="form-group">
                    <label for="correctAnswer">Correct Answer:</label>
                    <input type="text" class="form-control" name="correctAnswer" value="<?php echo $correctAnswer; ?>" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Update Quiz">
        </div>
    </form>
</div>

    <!-- Add these links to the <head> section of your HTML file -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
