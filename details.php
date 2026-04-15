<?php
// details.php  –  Show one user's full details
require_once 'config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: view.php'); exit; }

$stmt = getDB()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$u = $stmt->fetch();
if (!$u) { header('Location: view.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>User Details</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--primary:#1a56db;--border:#e5e7eb;--bg:#f3f4f6;--card:#fff;--text:#111827;--r:6px;}
body{font:14px/1.5 'Segoe UI',system-ui,sans-serif;background:var(--bg);
     color:var(--text);padding:32px 16px;}
.card{background:var(--card);border-radius:var(--r);box-shadow:0 1px 8px rgba(0,0,0,.1);
      max-width:520px;margin:0 auto;padding:28px 32px;}
h1{font-size:1.15rem;font-weight:700;color:var(--primary);margin-bottom:20px;}
table{width:100%;border-collapse:collapse;}
td{padding:8px 10px;border-bottom:1px solid var(--border);font-size:.9rem;}
td:first-child{font-weight:600;width:140px;color:#374151;}
.badge{display:inline-block;background:#e1effe;color:#1a56db;border-radius:12px;
       padding:2px 8px;font-size:.75rem;font-weight:600;margin:1px 2px;}
.btns{margin-top:20px;display:flex;gap:10px;}
.btn{padding:7px 20px;border-radius:var(--r);font:600 .85rem/1 inherit;
     cursor:pointer;border:none;text-decoration:none;}
.btn-primary{background:var(--primary);color:#fff;}
.btn-secondary{background:#e5e7eb;color:#111827;}
</style>
</head>
<body>
<div class="card">
  <h1>User #<?=htmlspecialchars($u['id'])?> Details</h1>
  <table>
    <tr><td>First Name</td><td><?=htmlspecialchars($u['first_name'])?></td></tr>
    <tr><td>Last Name</td> <td><?=htmlspecialchars($u['last_name'])?></td></tr>
    <tr><td>Address</td>   <td><?=nl2br(htmlspecialchars($u['address']))?></td></tr>
    <tr><td>Country</td>   <td><?=htmlspecialchars($u['country'])?></td></tr>
    <tr><td>Gender</td>    <td><?=htmlspecialchars($u['gender'])?></td></tr>
    <tr><td>Skills</td>
        <td><?php foreach(explode(',',$u['skills']) as $s): ?>
              <span class="badge"><?=htmlspecialchars(trim($s))?></span>
            <?php endforeach; ?></td></tr>
    <tr><td>Username</td>  <td><?=htmlspecialchars($u['username'])?></td></tr>
    <tr><td>Email</td>     <td><?=htmlspecialchars($u['email'])?></td></tr>
    <tr><td>Department</td><td><?=htmlspecialchars($u['department'])?></td></tr>
    <tr><td>Registered</td><td><?=htmlspecialchars($u['created_at'])?></td></tr>
  </table>
  <div class="btns">
    <a href="edit.php?id=<?=$u['id']?>" class="btn btn-primary">Edit</a>
    <a href="view.php" class="btn btn-secondary">← Back</a>
  </div>
</div>
</body>
</html>
