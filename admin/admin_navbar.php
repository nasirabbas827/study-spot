<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="admin_home.php">Admin Dashboard</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="update_admin.php">Update Profile</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="view_course_creator.php">Course Creators</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="view_teacher.php">Teachers</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="coursesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Courses
        </a>
        <div class="dropdown-menu" aria-labelledby="coursesDropdown">
          <a class="dropdown-item" href="view_courses.php">Courses</a>
          <a class="dropdown-item" href="add_lecture.php">Video</a>
          <a class="dropdown-item" href="add_quiz.php">Quiz</a>
          <a class="dropdown-item" href="add_pdf.php">PDF</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="enrolled_users.php">Enrolled Users</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="admin_reply.php">Customer Care</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>
