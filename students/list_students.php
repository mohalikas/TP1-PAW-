<?php
require_once __DIR__ . '/../db_connect.php';
$pdo = getDb();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: list_students.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
$students = $stmt->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Students</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<header class="topbar"><div class="container"><h1 class="brand">Attendance Manager</h1><nav class="nav"><a href="../index.php">Home</a> | <a href="add_student.php">Add Student</a></nav></div></header>
<main class="container">
  <section class="card">
    <h2>Students</h2>
    <?php if(empty($students)): ?><p>No students yet.</p><?php else: ?>
    <table><tr><th>ID</th><th>Full name</th><th>Matricule</th><th>Group</th><th>Actions</th></tr>
      <?php foreach($students as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><?= htmlspecialchars($s['fullname']) ?></td>
          <td><?= htmlspecialchars($s['matricule']) ?></td>
          <td><?= htmlspecialchars($s['group_id']) ?></td>
          <td>
            <a href="update_student.php?id=<?=$s['id']?>">Edit</a> |
            <a href="?delete=<?=$s['id']?>" onclick="return confirm('Delete?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    <?php endif; ?>
  </section>
</main>
</body></html>
