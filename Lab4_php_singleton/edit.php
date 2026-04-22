<?php
// edit.php  –  Edit an existing user
require_once 'config.php';
requireAuth();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
   ?: filter_input(INPUT_POST,'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: view.php'); exit; }

$stmt = getDB()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$u = $stmt->fetch();
if (!$u) { header('Location: view.php'); exit; }

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $address    = trim($_POST['address']    ?? '');
    $country    = trim($_POST['country']    ?? '');
    $gender     = trim($_POST['gender']     ?? '');
    $skills     = array_filter((array)($_POST['skills'] ?? []),
                      fn($s) => in_array($s, ALLOWED_SKILLS, true));
    $username   = trim($_POST['username']   ?? '');
    $email      = trim($_POST['email']      ?? '');
    $department = trim($_POST['department'] ?? 'OpenSource');

    if ($first_name === '')                          $errors['first_name'] = 'Required.';
    if ($last_name  === '')                          $errors['last_name']  = 'Required.';
    if ($address    === '')                          $errors['address']    = 'Required.';
    if (!in_array($country, COUNTRIES, true))        $errors['country']    = 'Select a country.';
    if (!in_array($gender, ['Male','Female'], true)) $errors['gender']     = 'Select a gender.';
    if (empty($skills))                              $errors['skills']     = 'Select at least one.';
    if (!preg_match('/^[a-zA-Z0-9_]{3,80}$/', $username))
                                                     $errors['username']   = '3–80 chars: letters, numbers, _.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
                                                     $errors['email']      = 'Invalid email.';

    // Check duplicate username (excluding self)
    if (empty($errors)) {
        $chk = getDB()->prepare('SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1');
        $chk->execute([$username, $id]);
        if ($chk->fetch()) $errors['username'] = 'Username already taken.';
    }

    if (empty($errors)) {
        $stmt = getDB()->prepare(
            'UPDATE users SET first_name=?,last_name=?,address=?,country=?,gender=?,
             skills=?,username=?,email=?,department=? WHERE id=?'
        );
        $stmt->execute([
            $first_name,$last_name,$address,$country,$gender,
            implode(',', array_values($skills)),
            $username,$email,$department ?: 'OpenSource',$id
        ]);
        header('Location: view.php');
        exit;
    }

    // Re-populate $u with posted values on error
    $u = array_merge($u, $_POST, ['skills' => implode(',', array_values($skills))]);
}

function v($k, $d=''): string { global $u; return htmlspecialchars($u[$k] ?? $d, ENT_QUOTES); }
function hasSkill($s): bool {
    global $u;
    return in_array($s, explode(',', $u['skills'] ?? ''), true);
}
function err($k): string {
    global $errors;
    return isset($errors[$k]) ? '<span class="err">'.$errors[$k].'</span>' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit User</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--primary:#1a56db;--primary-h:#1e429f;--danger:#e02424;
      --border:#d1d5db;--bg:#f3f4f6;--card:#fff;--text:#111827;--muted:#6b7280;--r:6px;}
body{font:14px/1.5 'Segoe UI',system-ui,sans-serif;background:var(--bg);
     color:var(--text);min-height:100vh;display:flex;align-items:center;
     justify-content:center;padding:24px 12px;}
.card{background:var(--card);border-radius:var(--r);box-shadow:0 2px 16px rgba(0,0,0,.1);
      width:100%;max-width:540px;padding:32px 36px;}
h1{font-size:1.15rem;font-weight:700;color:var(--primary);margin-bottom:22px;}
.row{display:grid;grid-template-columns:130px 1fr;align-items:start;gap:6px 10px;margin-bottom:10px;}
.row label{font-weight:500;padding-top:8px;font-size:.875rem;}
.row label .req{color:var(--danger);}
input[type=text],input[type=email],textarea,select{
  width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:var(--r);
  font:14px/1.5 inherit;background:#fafafa;color:var(--text);
  transition:border .15s,box-shadow .15s;}
input:focus,textarea:focus,select:focus{
  outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,86,219,.15);background:#fff;}
input.e,textarea.e,select.e{border-color:var(--danger);}
textarea{min-height:72px;resize:vertical;}
input[readonly]{background:#f9fafb;color:var(--muted);cursor:not-allowed;}
.opts{display:flex;flex-wrap:wrap;gap:14px;padding-top:8px;}
.opts label{font-weight:400;display:flex;align-items:center;gap:5px;cursor:pointer;padding-top:0;}
input[type=radio],input[type=checkbox]{width:15px;height:15px;accent-color:var(--primary);cursor:pointer;}
.err{color:var(--danger);font-size:.78rem;display:block;margin-top:2px;}
.btns{display:flex;gap:10px;justify-content:center;margin-top:22px;}
.btn{padding:8px 28px;border-radius:var(--r);font:600 .875rem/1 inherit;cursor:pointer;
     border:none;text-decoration:none;transition:background .15s;}
.btn-primary{background:var(--primary);color:#fff;}
.btn-primary:hover{background:var(--primary-h);}
.btn-secondary{background:#e5e7eb;color:var(--text);}
.btn-secondary:hover{background:#d1d5db;}
</style>
</head>
<body>
<div class="card">
  <h1>Edit User #<?=$id?></h1>
  <form method="POST" action="edit.php?id=<?=$id?>" novalidate>
    <input type="hidden" name="id" value="<?=$id?>">

    <div class="row"><label>First Name <span class="req">*</span></label>
      <div><input type="text" name="first_name" value="<?=v('first_name')?>"
                  class="<?=isset($errors['first_name'])?'e':''?>"><?=err('first_name')?></div></div>

    <div class="row"><label>Last Name <span class="req">*</span></label>
      <div><input type="text" name="last_name" value="<?=v('last_name')?>"
                  class="<?=isset($errors['last_name'])?'e':''?>"><?=err('last_name')?></div></div>

    <div class="row"><label>Address <span class="req">*</span></label>
      <div><textarea name="address"
                     class="<?=isset($errors['address'])?'e':''?>"><?=v('address')?></textarea><?=err('address')?></div></div>

    <div class="row"><label>Country <span class="req">*</span></label>
      <div><select name="country" class="<?=isset($errors['country'])?'e':''?>">
        <option value="">Select Country</option>
        <?php foreach(COUNTRIES as $c): ?>
          <option value="<?=htmlspecialchars($c)?>" <?=v('country')===$c?'selected':''?>>
            <?=htmlspecialchars($c)?></option>
        <?php endforeach; ?>
      </select><?=err('country')?></div></div>

    <div class="row"><label>Gender <span class="req">*</span></label>
      <div><div class="opts">
        <label><input type="radio" name="gender" value="Male"
                      <?=v('gender')==='Male'?'checked':''?>> Male</label>
        <label><input type="radio" name="gender" value="Female"
                      <?=v('gender')==='Female'?'checked':''?>> Female</label>
      </div><?=err('gender')?></div></div>

    <div class="row"><label>Skills <span class="req">*</span></label>
      <div><div class="opts">
        <label><input type="checkbox" name="skills[]" value="PHP"
                      <?=hasSkill('PHP')?'checked':''?>> PHP</label>
        <label><input type="checkbox" name="skills[]" value="J2SE"
                      <?=hasSkill('J2SE')?'checked':''?>> J2SE</label>
        <label><input type="checkbox" name="skills[]" value="MySQL"
                      <?=hasSkill('MySQL')?'checked':''?>> MySQL</label>
        <label><input type="checkbox" name="skills[]" value="PostgreSQL"
                      <?=hasSkill('PostgreSQL')?'checked':''?>> PostgreSQL</label>
      </div><?=err('skills')?></div></div>

    <div class="row"><label>Username <span class="req">*</span></label>
      <div><input type="text" name="username" value="<?=v('username')?>"
                  class="<?=isset($errors['username'])?'e':''?>"><?=err('username')?></div></div>

    <div class="row"><label>Email</label>
      <div><input type="email" name="email" value="<?=v('email')?>"
                  class="<?=isset($errors['email'])?'e':''?>"><?=err('email')?></div></div>

    <div class="row"><label>Department</label>
      <input type="text" name="department" value="<?=v('department','OpenSource')?>" readonly></div>

    <div class="btns">
      <button type="submit" class="btn btn-primary">Update</button>
      <a href="view.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>
