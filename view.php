<?php
// view.php  –  List all registered users
require_once 'config.php';

$users = getDB()->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Registered Users</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--primary:#1a56db;--danger:#e02424;--border:#e5e7eb;--bg:#f3f4f6;
      --card:#fff;--text:#111827;--muted:#6b7280;--r:6px;}
body{font:14px/1.5 'Segoe UI',system-ui,sans-serif;background:var(--bg);
     color:var(--text);padding:32px 16px;}
.wrap{max-width:960px;margin:0 auto;}
h1{font-size:1.3rem;font-weight:700;color:var(--primary);margin-bottom:20px;}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.btn{display:inline-block;padding:7px 20px;border-radius:var(--r);font:600 .85rem/1 inherit;
     cursor:pointer;border:none;text-decoration:none;transition:background .15s;}
.btn-primary{background:var(--primary);color:#fff;}
.btn-primary:hover{background:#1e429f;}
.btn-danger{background:var(--danger);color:#fff;font-size:.8rem;padding:5px 12px;}
.btn-danger:hover{background:#c81e1e;}
.btn-edit{background:#e5e7eb;color:var(--text);font-size:.8rem;padding:5px 12px;}
.btn-edit:hover{background:#d1d5db;}
table{width:100%;border-collapse:collapse;background:var(--card);
      border-radius:var(--r);overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.08);}
thead{background:#1e429f;color:#fff;}
th,td{padding:10px 14px;text-align:left;border-bottom:1px solid var(--border);}
th{font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;}
td{font-size:.88rem;vertical-align:top;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:#f9fafb;}
.badge{display:inline-block;background:#e1effe;color:#1a56db;border-radius:12px;
       padding:2px 8px;font-size:.75rem;font-weight:600;margin:1px 2px;}
.no-data{text-align:center;padding:40px;color:var(--muted);}
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <h1>Registered Users (<?=count($users)?>)</h1>
    <a href="login.php" class="btn btn-primary">+ New Registration</a>
  </div>

  <?php if (empty($users)): ?>
    <p class="no-data">No users registered yet.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Country</th>
        <th>Gender</th>
        <th>Skills</th>
        <th>Dept</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?=htmlspecialchars($u['id'])?></td>
        <td><?=htmlspecialchars($u['first_name'].' '.$u['last_name'])?></td>
        <td><?=htmlspecialchars($u['username'])?></td>
        <td><?=htmlspecialchars($u['email'])?></td>
        <td><?=htmlspecialchars($u['country'])?></td>
        <td><?=htmlspecialchars($u['gender'])?></td>
        <td>
          <?php foreach (explode(',', $u['skills']) as $sk): ?>
            <span class="badge"><?=htmlspecialchars(trim($sk))?></span>
          <?php endforeach; ?>
        </td>
        <td><?=htmlspecialchars($u['department'])?></td>
        <td style="white-space:nowrap">
          <a href="details.php?id=<?=$u['id']?>" class="btn btn-edit">View</a>
          <a href="edit.php?id=<?=$u['id']?>"    class="btn btn-edit">Edit</a>
          <a href="remove.php?id=<?=$u['id']?>"  class="btn btn-danger"
             onclick="return confirm('Delete this user?')">Del</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
</body>
</html>
