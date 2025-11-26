<?php
require_once __DIR__ . '/../db_connect.php';
$pdo = getDb();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: list_students.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) { die("Student not found."); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $matricule = trim($_POST['matricule'] ?? '');
    $group_id = trim($_POST['group_id'] ?? '');

    if ($fullname === '') $errors[] = "Full name required.";
    if ($matricule === '') $errors[] = "Matricule required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE students SET fullname = ?, matricule = ?, group_id = ? WHERE id = ?");
            $stmt->execute([$fullname, $matricule, $group_id ?: null, $id]);
            $success = "Updated successfully.";
            $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
            $stmt->execute([$id]);
            $student = $stmt->fetch();
        } catch (PDOException $e) {
            file_put_contents(__DIR__.'/../error.log', "[".date('Y-m-d H:i:s')."] ".$e->getMessage().PHP_EOL, FILE_APPEND);
            $errors[] = "Database error or duplicate matricule.";
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<header class="topbar"><div class="container"><h1 class="brand">Attendance Manager</h1><nav class="nav"><a href="list_students.php">Back to list</a></nav></div></header>
<main class="container">
  <section class="card">
    <h2>Edit Student</h2>
    <?php if ($errors): ?><div style="color:#ffb4b4"><ul><?php foreach($errors as $e):?><li><?=htmlspecialchars($e)?></li><?php endforeach;?></ul></div><?php endif; ?>
    <?php if (!empty($success)): ?><div style="color:#a7f3d0"><?=htmlspecialchars($success)?></div><?php endif; ?>
    <form method="post">
      <label>Full name:<input name="fullname" required value="<?=htmlspecialchars($student['fullname'])?>"></label>
      <label>Matricule:<input name="matricule" required value="<?=htmlspecialchars($student['matricule'])?>"></label>
      <label>Group:<input name="group_id" value="<?=htmlspecialchars($student['group_id'])?>"></label>
      <div style="margin-top:12px"><button class="btn" type="submit">Save</button> <a class="btn outline" href="list_students.php">Cancel</a></div>
    </form>
  </section>
</main>
</body></html>
