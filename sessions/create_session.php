<?php
require_once __DIR__ . '/../db_connect.php';
$pdo = getDb();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = trim($_POST['course_id'] ?? '');
    $group_id = trim($_POST['group_id'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $opened_by = trim($_POST['opened_by'] ?? '');

    if ($course_id === '') $errors[] = "Course required.";
    if ($date === '') $errors[] = "Date required.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status) VALUES (?, ?, ?, ?, 'open')");
        $stmt->execute([$course_id, $group_id ?: null, $date, $opened_by ?: null]);
        $session_id = $pdo->lastInsertId();
        $success = "Session created (ID: $session_id).";
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Create Session</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<header class="topbar"><div class="container"><h1 class="brand">Attendance Manager</h1><nav class="nav"><a href="../index.php">Home</a> | <a href="list_sessions.php">Sessions</a></nav></div></header>
<main class="container">
  <section class="card">
    <h2>Create Session</h2>
    <?php if(!empty($errors)):?><div style="color:#ffb4b4"><?=implode('<br>',$errors)?></div><?php endif;?>
    <?php if(!empty($success)):?><div style="color:#a7f3d0"><?=htmlspecialchars($success)?></div><?php endif;?>
    <form method="post">
      <label>Course ID:<input name="course_id" required value="<?=htmlspecialchars($_POST['course_id'] ?? '')?>"></label>
      <label>Group:<input name="group_id" value="<?=htmlspecialchars($_POST['group_id'] ?? '')?>"></label>
      <label>Date:<input type="date" name="date" required value="<?=htmlspecialchars($_POST['date'] ?? date('Y-m-d'))?>"></label>
      <label>Opened by (professor):<input name="opened_by" value="<?=htmlspecialchars($_POST['opened_by'] ?? '')?>"></label>
      <div style="margin-top:12px"><button class="btn" type="submit">Create</button> <a class="btn outline" href="list_sessions.php">Back</a></div>
    </form>
  </section>
</main>
</body></html>
