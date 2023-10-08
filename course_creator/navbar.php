<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="creator_dashboard.php">Course Creator Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION["usertype"]) && $_SESSION["usertype"] === "creator") { ?>
                <li class="nav-item">
                    <a class="nav-link" href="view_courses.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_lecture.php">Lectures</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_quiz.php">Quiz</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_pdf.php">PDF</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php } else { ?>
                <li class="nav-item">
                    <a class="nav-link" href="creator_login.php">Login</a>
                </li>
            <?php } ?>
        </ul>
    </div>
</nav>
