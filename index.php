<?php
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Attendance Manager</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar">
    <div class="container">
      <h1 class="brand">Attendance Manager</h1>
      <nav class="nav">
        <a href="students/list_students.php">Students</a>
        <a href="sessions/list_sessions.php">Sessions</a>
        <a href="take_attendance.php">Take Attendance</a>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="card">
      <h2>Welcome</h2>
      <p>Use the menu to manage students, create sessions, and take attendance.</p>
      <div class="grid">
        <a class="btn" href="students/add_student.php">Add Student</a>
        <a class="btn outline" href="sessions/create_session.php">Create Session</a>
        <a class="btn ghost" href="sessions/list_sessions.php">View Sessions</a>
      </div>
    </section>
  </main>

 
</body>
</html>
