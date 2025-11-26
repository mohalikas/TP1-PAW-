<?php
require_once __DIR__ . '/../db_connect.php';
$pdo = getDb();

if (isset($_GET['close'])) {
    $id = (int)$_GET['close'];
    $stmt = $pdo->prepare("UPDATE attendance_sessions SET status = 'closed' WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: list_sessions.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM attendance_sessions ORDER BY date DESC, id DESC");
$sessions = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Sessions</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<header class="topbar"><div class="container"><h1 class="brand">Attendance Manager</h1><nav class="nav"><a href="../index.php">Home</a> | <a href="create_session.php">Create Session</a></nav></div></header>
<main class="container">
  <section class="card">
    <h2>Sessions</h2>
    <?php if(empty($sessions)): ?><p>No sessions yet.</p><?php else: ?>
    <table><tr><th>ID</th><th>Course</th><th>Group</th><th>Date</th><th>Opened by</th><th>Status</th><th>Actions</th></tr>
      <?php foreach($sessions as $s): ?>
        <tr>
          <td><?=$s['id']?></td>
          <td><?=htmlspecialchars($s['course_id'])?></td>
          <td><?=htmlspecialchars($s['group_id'])?></td>
          <td><?=$s['date']?></td>
          <td><?=htmlspecialchars($s['opened_by'])?></td>
          <td><?=$s['status']?></td>
          <td>
            <?php if($s['status'] === 'open'): ?>
              <a href="../../take_attendance.php?session_id=<?=$s['id']?>">Take</a> |
              <a href="?close=<?=$s['id']?>" onclick="return confirm('Close session?')">Close</a>
            <?php else: ?>
              <a href="../../take_attendance.php?session_id=<?=$s['id']?>">View</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach;?>
    </table>
    <?php endif;?>
  </section>
</main>
</body></html>
