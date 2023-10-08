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

// Function to fetch quizzes for a course
function getQuizzes($conn, $course_id) {
    $query = "SELECT * FROM quizzes WHERE CourseID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result;
}

// Function to delete quiz results for a specific user and course
function deleteQuizResults($conn, $user_id, $course_id) {
    $deleteQuery = "DELETE FROM quiz_results WHERE UserID = ? AND CourseID = ?";
    $stmt = mysqli_prepare($conn, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_affected_rows($stmt) > 0; // Check if any rows were affected (deleted)
}

// Function to fetch user's total score for the course
function getUserTotalScore($conn, $user_id, $course_id) {
    $query = "SELECT TotalScore FROM quiz_results WHERE UserID = ? AND CourseID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row["TotalScore"];
    }
    return 0;
}

// Check if the quiz form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["retake_quiz"])) {
        // User clicked "Retake Quiz" button

        // Delete quiz results for the user for the specific course
        $deleted = deleteQuizResults($conn, $user_id, $course_id);

        if ($deleted) {
            echo "<div class='alert alert-success'>Quiz results reset. You can retake the quiz.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error resetting quiz results.</div>";
        }
    } else {
        // User submitted quiz answers
        $quizAnswers = $_POST["answers"];
        $totalQuizzesAttempted = count($quizAnswers); // Count the number of quizzes attempted
        $totalScore = 0; // Initialize the total score

        foreach ($quizAnswers as $quiz_id => $selected_option) {
            // Calculate scores and update the total score
            $query = "SELECT CorrectAnswer FROM quizzes WHERE QuizID = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $quiz_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $correct_answer = $row["CorrectAnswer"];
                $score = ($selected_option == $correct_answer) ? 1 : 0;
                $totalScore += $score;
            }
        }

        // Insert the user's total score and total quizzes attempted into the database
        $insertQuery = "INSERT INTO quiz_results (UserID, CourseID, TotalScore, TotalQuizzesAttempted) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "iiii", $user_id, $course_id, $totalScore, $totalQuizzesAttempted);
        mysqli_stmt_execute($stmt);

        // Display total score and total quizzes attempted
        echo "<div class='alert alert-info'>Total Score: $totalScore</div>";
        echo "<div class='alert alert-info'>Total Quizzes Attempted: $totalQuizzesAttempted</div>";

        // You can also save individual quiz scores here if needed

        // Redirect or show a message as needed
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quizzes</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Quizzes</h2>
        <?php
        // Check if the user has already attempted the quiz
        $userTotalScore = getUserTotalScore($conn, $user_id, $course_id);
        $quizzes = getQuizzes($conn, $course_id);

        if ($userTotalScore > 0) {
            echo "<div class='alert alert-info'>Your Total Score: $userTotalScore</div>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='retake_quiz' value='1'>"; // Indicate it's a retake
            echo "<button class='btn btn-warning' type='submit'>Retake Quiz</button>";
            echo "</form>";
        } else {
            // Display the quiz form
            echo "<form method='post'>";
            if (mysqli_num_rows($quizzes) > 0) {
                while ($quiz = mysqli_fetch_assoc($quizzes)) {
                    echo "<ul>";
                    echo "<li>" . $quiz["Question"] . "</li>";
                    echo "<ul>";
                    echo "<li><input type='radio' name='answers[" . $quiz["QuizID"] . "]' value='1'> Option 1: " . $quiz["Option1"] . "</li>";
                    echo "<li><input type='radio' name='answers[" . $quiz["QuizID"] . "]' value='2'> Option 2: " . $quiz["Option2"] . "</li>";
                    echo "<li><input type='radio' name='answers[" . $quiz["QuizID"] . "]' value='3'> Option 3: " . $quiz["Option3"] . "</li>";
                    echo "<li><input type='radio' name='answers[" . $quiz["QuizID"] . "]' value='4'> Option 4: " . $quiz["Option4"] . "</li>";
                    echo "</ul>";
                    echo "</ul>";
                }
                echo "<input type='submit' class='btn btn-primary' value='Submit All Quizzes'>";
            } else {
                echo "No quizzes available for this course.";
            }
            echo "</form>";
        }
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>
