<?php
require_once __DIR__ . '/../db_connect.php';
$pdo = getDb();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $matricule = trim($_POST['matricule'] ?? '');
    $group_id = trim($_POST['group_id'] ?? '');

    if ($fullname === '') $errors[] = "Full name required.";
    if ($matricule === '') $errors[] = "Matricule required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (fullname, matricule, group_id) VALUES (?, ?, ?)");
            $stmt->execute([$fullname, $matricule, $group_id ?: null]);
            $success = "Student added successfully.";
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = "Matricule already exists.";
            } else {
                file_put_contents(__DIR__.'/../error.log', "[".date('Y-m-d H:i:s')."] ".$e->getMessage().PHP_EOL, FILE_APPEND);
                $errors[] = "Database error.";
            }
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Add Student</title><link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="topbar"><div class="container"><h1 class="brand">Attendance Manager</h1><nav class="nav"><a href="../index.php">Home</a></nav></div></header>
<main class="container">
  <section class="card">
    <h2>Add Student</h2>
    <?php if (!empty($errors)): ?>
      <div style="color:#ffb4b4"><ul><?php foreach($errors as $er):?><li><?=htmlspecialchars($er)?></li><?php endforeach;?></ul></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?><div style="color:#a7f3d0"><?=htmlspecialchars($success)?></div><?php endif;?>
    <form method="post">
      <label>Full name:<input name="fullname" required value="<?=htmlspecialchars($_POST['fullname'] ?? '')?>"></label>
      <label>Matricule:<input name="matricule" required value="<?=htmlspecialchars($_POST['matricule'] ?? '')?>"></label>
      <label>Group:<input name="group_id" value="<?=htmlspecialchars($_POST['group_id'] ?? '')?>"></label>
      <div style="margin-top:12px"><button class="btn" type="submit">Add</button> <a class="btn outline" href="list_students.php">Back to list</a></div>
    </form>
  </section>
</main>
</body>
</html>
