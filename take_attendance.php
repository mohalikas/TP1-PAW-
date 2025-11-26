<?php
require_once __DIR__ . '/db_connect.php';
$pdo = getDb();

$session_id = isset($_GET['session_id']) ? (int)$_GET['session_id'] : null;

if (!$session_id) {
    $stmt = $pdo->query("SELECT id FROM attendance_sessions WHERE status = 'open' ORDER BY date DESC, id DESC LIMIT 1");
    $r = $stmt->fetch();
    if ($r) $session_id = $r['id'];
}

$session = null;
if ($session_id) {
    $stmt = $pdo->prepare("SELECT * FROM attendance_sessions WHERE id = ?");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch();
}

if (!$session) {
    die("No session available. Create a session first from Sessions -> Create Session.");
}

if ($session['group_id']) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE group_id = ? ORDER BY fullname");
    $stmt->execute([$session['group_id']]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY fullname");
}
$students = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM attendance_records WHERE session_id = ?");
$stmt->execute([$session_id]);
$alreadyCount = (int)$stmt->fetchColumn();

$errors = [];
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $session['status'] === 'open') {
    $pdo->beginTransaction();
    try {
        foreach ($students as $st) {
            $key = 'status_' . $st['id'];
            $status = ($_POST[$key] ?? 'absent') === 'present' ? 'present' : 'absent';
            $ins = $pdo->prepare("INSERT INTO attendance_records (session_id, student_id, status) VALUES (?, ?, ?)");
            $ins->execute([$session_id, $st['id'], $status]);
        }
        $pdo->commit();
        $success = "Attendance saved for session #{$session_id}.";
    } catch (PDOException $e) {
        $pdo->rollBack();
        file_put_contents(__DIR__.'/error.log', "[".date('Y-m-d H:i:s')."] ".$e->getMessage().PHP_EOL, FILE_APPEND);
        $errors[] = "Failed to save attendance.";
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM attendance_records WHERE session_id = ?");
    $stmt->execute([$session_id]);
    $alreadyCount = (int)$stmt->fetchColumn();
}

$records = [];
if ($alreadyCount > 0) {
    $stmt = $pdo->prepare("SELECT ar.*, s.fullname, s.matricule FROM attendance_records ar JOIN students s ON s.id = ar.student_id WHERE ar.session_id = ?");
    $stmt->execute([$session_id]);
    $records = $stmt->fetchAll();
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Take Attendance</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<header class="topbar"><div class="container"><h1 class="brand">Attendance Manager</h1><nav class="nav"><a href="index.php">Home</a> | <a href="sessions/list_sessions.php">Sessions</a></nav></div></header>
<main class="container">
  <section class="card">
    <h2>Take Attendance - Session #<?=htmlspecialchars($session['id'])?></h2>
    <p>Course: <?=htmlspecialchars($session['course_id'])?> | Date: <?=htmlspecialchars($session['date'])?> | Status: <?=htmlspecialchars($session['status'])?></p>

    <?php if($errors): ?><div style="color:#ffb4b4"><?=implode('<br>',$errors)?></div><?php endif;?>
    <?php if($success): ?><div style="color:#a7f3d0"><?=htmlspecialchars($success)?></div><?php endif; ?>

    <?php if ($alreadyCount > 0): ?>
      <h3>Recorded attendance (<?=count($records)?>):</h3>
      <table><tr><th>Student</th><th>Matricule</th><th>Status</th><th>Time</th></tr>
        <?php foreach($records as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['fullname'])?></td>
            <td><?=htmlspecialchars($r['matricule'])?></td>
            <td><?=htmlspecialchars($r['status'])?></td>
            <td><?=htmlspecialchars($r['recorded_at'])?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <?php if ($session['status'] !== 'open'): ?>
        <p>Session is closed. Attendance cannot be taken.</p>
      <?php else: ?>
        <form method="post">
          <table>
            <tr><th>Student</th><th>Matricule</th><th>Present</th><th>Absent</th></tr>
            <?php foreach ($students as $st): ?>
              <tr>
                <td><?=htmlspecialchars($st['fullname'])?></td>
                <td><?=htmlspecialchars($st['matricule'])?></td>
                <td><input type="radio" name="status_<?=$st['id']?>" value="present"></td>
                <td><input type="radio" name="status_<?=$st['id']?>" value="absent" checked></td>
              </tr>
            <?php endforeach;?>
          </table>
          <div style="margin-top:12px"><button class="btn" type="submit">Save Attendance</button></div>
        </form>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
