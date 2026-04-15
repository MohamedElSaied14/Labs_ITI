<?php
// login.php  –  Registration form
require_once 'config.php';

$errors  = [];
$success = false;
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name']  ?? '');
    $address    = trim($_POST['address']    ?? '');
    $country    = trim($_POST['country']    ?? '');
    $gender     = trim($_POST['gender']     ?? '');
    $skills     = array_values(array_filter(
                      (array)($_POST['skills'] ?? []),
                      fn($s) => in_array($s, ALLOWED_SKILLS, true)
                  ));
    $username   = trim($_POST['username']   ?? '');
    $password   = $_POST['password']        ?? '';
    $department = trim($_POST['department'] ?? 'OpenSource');
    $email      = trim($_POST['email']      ?? '');

    // --- Validate ---
    if ($first_name === '')                              $errors['first_name'] = 'Required.';
    if ($last_name  === '')                              $errors['last_name']  = 'Required.';
    if ($address    === '')                              $errors['address']    = 'Required.';
    if (!in_array($country, COUNTRIES, true))            $errors['country']    = 'Select a valid country.';
    if (!in_array($gender, ['Male','Female'], true))     $errors['gender']     = 'Select a gender.';
    if (empty($skills))                                  $errors['skills']     = 'Select at least one skill.';
    if ($username === '')                                $errors['username']   = 'Required.';
    elseif (!preg_match('/^[a-zA-Z0-9_]{3,80}$/', $username))
                                                         $errors['username']   = '3-80 chars: letters, numbers, _.';
    if (strlen($password) < 6)                           $errors['password']   = 'Minimum 6 characters.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
                                                         $errors['email']      = 'Invalid email address.';

    // --- Check duplicate username ---
    if (empty($errors)) {
        $stmt = getDB()->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        if ($stmt->fetch()) $errors['username'] = 'Username already taken.';
    }

    // --- Insert ---
    if (empty($errors)) {
        $stmt = getDB()->prepare(
            'INSERT INTO users
               (first_name, last_name, address, country, gender, skills, username, password, department, email)
             VALUES (?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $first_name, $last_name, $address, $country, $gender,
            implode(',', $skills),
            $username,
            password_hash($password, PASSWORD_BCRYPT),
            $department ?: 'OpenSource',
            $email,
        ]);
        $success = true;
        $old = [];
    }
}

function v($k, $d = ''): string {
    global $old;
    return htmlspecialchars($old[$k] ?? $d, ENT_QUOTES);
}
function hasSkill(string $s): bool {
    global $old;
    return in_array($s, (array)($old['skills'] ?? []), true);
}
function err(string $k): string {
    global $errors;
    return isset($errors[$k])
        ? '<span class="err">'.htmlspecialchars($errors[$k]).'</span>'
        : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Registration</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#1a56db;--primary-h:#1e429f;--danger:#e02424;--success:#057a55;
  --border:#d1d5db;--bg:#f3f4f6;--card:#fff;--text:#111827;--muted:#6b7280;--r:6px;
}
body{font:14px/1.5 'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);
     min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px 12px;}
.card{background:var(--card);border-radius:var(--r);box-shadow:0 2px 16px rgba(0,0,0,.1);
      width:100%;max-width:540px;padding:32px 36px;}
h1{font-size:1.25rem;font-weight:700;text-align:center;color:var(--primary);margin-bottom:24px;}

.success-box{background:#def7ec;border:1px solid #84e1bc;color:var(--success);
             border-radius:var(--r);padding:12px 16px;text-align:center;
             margin-bottom:20px;font-weight:600;}
.success-box a{color:var(--primary);}

.row{display:grid;grid-template-columns:130px 1fr;align-items:start;gap:6px 10px;margin-bottom:12px;}
.row>label{font-weight:500;padding-top:8px;font-size:.875rem;}
.row label .req{color:var(--danger);}

input[type=text],input[type=email],input[type=password],textarea,select{
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

.err{color:var(--danger);font-size:.78rem;display:block;margin-top:3px;}

.btns{display:flex;gap:10px;justify-content:center;margin-top:24px;}
.btn{padding:8px 32px;border-radius:var(--r);font:600 .9rem/1 inherit;cursor:pointer;border:none;
     transition:background .15s,transform .1s;}
.btn:active{transform:scale(.97);}
.btn-primary{background:var(--primary);color:#fff;}
.btn-primary:hover{background:var(--primary-h);}
.btn-reset{background:#e5e7eb;color:var(--text);}
.btn-reset:hover{background:#d1d5db;}

.footer-link{text-align:center;margin-top:16px;font-size:.82rem;color:var(--muted);}
.footer-link a{color:var(--primary);text-decoration:none;font-weight:500;}
.footer-link a:hover{text-decoration:underline;}
</style>
</head>
<body>
<div class="card">
  <h1>Registration</h1>

  <?php if ($success): ?>
    <div class="success-box">
      &#10003; Registered successfully!
      &nbsp;<a href="view.php">View Users</a>
    </div>
  <?php endif; ?>

  <form method="POST" action="login.php" novalidate>

    <div class="row">
      <label for="fn">First Name <span class="req">*</span></label>
      <div>
        <input type="text" id="fn" name="first_name" value="<?=v('first_name')?>"
               class="<?=isset($errors['first_name'])?'e':''?>">
        <?=err('first_name')?>
      </div>
    </div>

    <div class="row">
      <label for="ln">Last Name <span class="req">*</span></label>
      <div>
        <input type="text" id="ln" name="last_name" value="<?=v('last_name')?>"
               class="<?=isset($errors['last_name'])?'e':''?>">
        <?=err('last_name')?>
      </div>
    </div>

    <div class="row">
      <label for="addr">Address <span class="req">*</span></label>
      <div>
        <textarea id="addr" name="address"
                  class="<?=isset($errors['address'])?'e':''?>"><?=v('address')?></textarea>
        <?=err('address')?>
      </div>
    </div>

    <div class="row">
      <label for="country">Country <span class="req">*</span></label>
      <div>
        <select id="country" name="country"
                class="<?=isset($errors['country'])?'e':''?>">
          <option value="">Select Country</option>
          <?php foreach (COUNTRIES as $c): ?>
            <option value="<?=htmlspecialchars($c)?>"
              <?=v('country')===$c?'selected':''?>><?=htmlspecialchars($c)?></option>
          <?php endforeach; ?>
        </select>
        <?=err('country')?>
      </div>
    </div>

    <div class="row">
      <label>Gender <span class="req">*</span></label>
      <div>
        <div class="opts">
          <label><input type="radio" name="gender" value="Male"
                        <?=v('gender')==='Male'?'checked':''?>> Male</label>
          <label><input type="radio" name="gender" value="Female"
                        <?=v('gender')==='Female'?'checked':''?>> Female</label>
        </div>
        <?=err('gender')?>
      </div>
    </div>

    <div class="row">
      <label>Skills <span class="req">*</span></label>
      <div>
        <div class="opts">
          <label><input type="checkbox" name="skills[]" value="PHP"
                        <?=hasSkill('PHP')?'checked':''?>> PHP</label>
          <label><input type="checkbox" name="skills[]" value="J2SE"
                        <?=hasSkill('J2SE')?'checked':''?>> J2SE</label>
          <label><input type="checkbox" name="skills[]" value="MySQL"
                        <?=hasSkill('MySQL')?'checked':''?>> MySQL</label>
          <label><input type="checkbox" name="skills[]" value="PostgreSQL"
                        <?=hasSkill('PostgreSQL')?'checked':''?>> PostgreSQL</label>
        </div>
        <?=err('skills')?>
      </div>
    </div>

    <div class="row">
      <label for="uname">Username <span class="req">*</span></label>
      <div>
        <input type="text" id="uname" name="username" value="<?=v('username')?>"
               class="<?=isset($errors['username'])?'e':''?>">
        <?=err('username')?>
      </div>
    </div>

    <div class="row">
      <label for="pw">Password <span class="req">*</span></label>
      <div>
        <input type="password" id="pw" name="password"
               class="<?=isset($errors['password'])?'e':''?>">
        <?=err('password')?>
      </div>
    </div>

    <div class="row">
      <label for="email">Email</label>
      <div>
        <input type="email" id="email" name="email" value="<?=v('email')?>"
               class="<?=isset($errors['email'])?'e':''?>">
        <?=err('email')?>
      </div>
    </div>

    <div class="row">
      <label for="dept">Department</label>
      <input type="text" id="dept" name="department"
             value="<?=v('department','OpenSource')?>" readonly>
    </div>

    <div class="btns">
      <button type="submit" class="btn btn-primary">Submit</button>
      <button type="reset"  class="btn btn-reset">Reset</button>
    </div>
  </form>

  <div class="footer-link"><a href="view.php">View all registered users &rarr;</a></div>
</div>
</body>
</html>
