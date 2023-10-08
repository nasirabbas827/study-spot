<?php
session_start();
include('config.php');

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: login.php");
    exit;
}

// Delete a message if the user is the sender
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $message_id = $_GET['delete'];
    $user_id = $_SESSION["id"];

    $sql_delete = "DELETE FROM messages WHERE id = ? AND sender_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $message_id, $user_id);
    $stmt_delete->execute();
    $stmt_delete->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Messages</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<?php
include('navbar.php');
?>

<div class="container mt-5">
    <h2 class="text-center">All Messages</h2>
    <?php
    // Retrieve all messages with sender's username
    $sql_all_messages = "SELECT messages.*, users.username FROM messages
                        INNER JOIN users ON messages.sender_id = users.id";
    $result_all_messages = $conn->query($sql_all_messages);

    while ($row = $result_all_messages->fetch_assoc()) {
        echo '<div class="card mb-3">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">Message from ' . $row['username'] . '</h5>';
        echo '<p class="card-text">' . $row['message_text'] . '</p>';
        if ($row['reply_text']) {
            echo '<h6 class="card-subtitle mb-2 text-muted">Admin\'s Reply</h6>';
            echo '<p class="card-text">' . $row['reply_text'] . '</p>';
        }

        // Display the delete button only if the user is the sender
        if ($row['sender_id'] == $_SESSION["id"]) {
            echo '<a href="view_messages.php?delete=' . $row['id'] . '" class="btn btn-danger">Delete Message</a>';
        }
        echo '</div>';
        echo '</div>';
    }
    ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
