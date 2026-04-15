<?php
// view.php  –  List all registered users (auth required)
require_once 'config.php';
requireAuth();

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
.wrap{max-width:1020px;margin:0 auto;}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
h1{font-size:1.3rem;font-weight:700;color:var(--primary);}
.topbar-right{display:flex;align-items:center;gap:10px;font-size:.82rem;color:var(--muted);}
.topbar-right strong{color:var(--text);}
.btn{display:inline-block;padding:7px 18px;border-radius:var(--r);font:600 .85rem/1 inherit;
     cursor:pointer;border:none;text-decoration:none;transition:background .15s;}
.btn-primary{background:var(--primary);color:#fff;}
.btn-primary:hover{background:#1e429f;}
.btn-danger{background:var(--danger);color:#fff;font-size:.8rem;padding:5px 12px;}
.btn-danger:hover{background:#c81e1e;}
.btn-edit{background:#e5e7eb;color:var(--text);font-size:.8rem;padding:5px 12px;}
.btn-edit:hover{background:#d1d5db;}
.btn-logout{background:#fff;color:var(--muted);border:1px solid var(--border);padding:6px 14px;}
.btn-logout:hover{background:#f9fafb;color:var(--danger);}
table{width:100%;border-collapse:collapse;background:var(--card);
      border-radius:var(--r);overflow:hidden;box-shadow:0 1px 8px rgba(0,0,0,.08);}
thead{background:#1e429f;color:#fff;}
th,td{padding:10px 14px;text-align:left;border-bottom:1px solid var(--border);}
th{font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;}
td{font-size:.88rem;vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:#f9fafb;}
.badge{display:inline-block;background:#e1effe;color:#1a56db;border-radius:12px;
       padding:2px 8px;font-size:.75rem;font-weight:600;margin:1px 2px;}
.no-data{text-align:center;padding:40px;color:var(--muted);}
.avatar{width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--border);}
.avatar-placeholder{width:36px;height:36px;border-radius:50%;background:#e5e7eb;
                    display:inline-flex;align-items:center;justify-content:center;
                    font-size:1rem;color:var(--muted);}
</style>
</head>
<body>
<div class="wrap">
  <div class="topbar">
    <h1>&#128101; Registered Users (<?=count($users)?>)</h1>
    <div class="topbar-right">
      <span>Logged in as <strong><?=htmlspecialchars($_SESSION['user_username'])?></strong></span>
      <a href="register.php" class="btn btn-primary">+ New Registration</a>
      <a href="logout.php"   class="btn btn-logout">Sign Out</a>
    </div>
  </div>

  <?php if (empty($users)): ?>
    <p class="no-data">No users registered yet.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Photo</th>
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
        <td>
          <?php if (!empty($u['profile_image']) && file_exists(UPLOAD_DIR . $u['profile_image'])): ?>
            <img src="<?=UPLOAD_URL.htmlspecialchars($u['profile_image'])?>"
                 class="avatar" alt="<?=htmlspecialchars($u['first_name'])?>">
          <?php else: ?>
            <span class="avatar-placeholder">&#128100;</span>
          <?php endif; ?>
        </td>
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
