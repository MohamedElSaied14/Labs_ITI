<?php
// register.php – Registration form (with profile image upload)
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
    $confirm    = $_POST['confirm']         ?? '';
    $department = trim($_POST['department'] ?? 'OpenSource');
    $email      = trim($_POST['email']      ?? '');

    // ── Validate text fields ──────────────────────────────────────────────────
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
    elseif ($password !== $confirm)                      $errors['confirm']    = 'Passwords do not match.';
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
                                                         $errors['email']      = 'Invalid email address.';

    // ── Validate image upload ─────────────────────────────────────────────────
    $savedImage = '';
    $imgFile    = $_FILES['profile_image'] ?? null;

    if ($imgFile && $imgFile['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($imgFile['error'] !== UPLOAD_ERR_OK) {
            $errors['profile_image'] = 'Upload error (code ' . $imgFile['error'] . ').';
        } elseif ($imgFile['size'] > MAX_IMG_SIZE) {
            $errors['profile_image'] = 'Image must be under 2 MB.';
        } else {
            $mime = mime_content_type($imgFile['tmp_name']);
            if (!in_array($mime, ALLOWED_IMG_TYPES, true)) {
                $errors['profile_image'] = 'Only JPEG, PNG, GIF, or WebP allowed.';
            }
        }
    }

    // ── Check duplicate username ──────────────────────────────────────────────
    if (empty($errors)) {
        $stmt = getDB()->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        if ($stmt->fetch()) $errors['username'] = 'Username already taken.';
    }

    // ── Move image & insert ───────────────────────────────────────────────────
    if (empty($errors)) {
        // Create uploads dir if missing
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

        if ($imgFile && $imgFile['error'] === UPLOAD_ERR_OK) {
            $ext        = pathinfo($imgFile['name'], PATHINFO_EXTENSION) ?: 'jpg';
            $filename   = 'user_' . bin2hex(random_bytes(8)) . '.' . strtolower($ext);
            $destPath   = UPLOAD_DIR . $filename;
            if (move_uploaded_file($imgFile['tmp_name'], $destPath)) {
                $savedImage = $filename;
            } else {
                $errors['profile_image'] = 'Could not save image. Check folder permissions.';
            }
        }
    }

    if (empty($errors)) {
        $stmt = getDB()->prepare(
            'INSERT INTO users
               (first_name, last_name, address, country, gender, skills,
                username, password, department, email, profile_image)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $first_name, $last_name, $address, $country, $gender,
            implode(',', $skills),
            $username,
            password_hash($password, PASSWORD_BCRYPT),
            $department ?: 'OpenSource',
            $email,
            $savedImage,
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
      width:100%;max-width:560px;padding:32px 36px;}
h1{font-size:1.25rem;font-weight:700;text-align:center;color:var(--primary);margin-bottom:24px;}

.success-box{background:#def7ec;border:1px solid #84e1bc;color:var(--success);
             border-radius:var(--r);padding:12px 16px;text-align:center;
             margin-bottom:20px;font-weight:600;}
.success-box a{color:var(--primary);}

.row{display:grid;grid-template-columns:140px 1fr;align-items:start;gap:6px 10px;margin-bottom:12px;}
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

/* Image upload area */
.img-upload-wrap{display:flex;align-items:center;gap:14px;}
.img-preview{width:72px;height:72px;border-radius:50%;object-fit:cover;
             border:2px solid var(--border);background:#f9fafb;display:flex;
             align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;}
.img-preview img{width:100%;height:100%;object-fit:cover;}
.img-preview .placeholder{font-size:2rem;color:var(--muted);}
.img-upload-btn{display:inline-block;padding:7px 14px;background:#f3f4f6;border:1px solid var(--border);
                border-radius:var(--r);font:500 .85rem/1 inherit;cursor:pointer;transition:background .15s;}
.img-upload-btn:hover{background:#e5e7eb;}
input[type=file]{display:none;}

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
  <h1>&#128221; Registration</h1>

  <?php if ($success): ?>
    <div class="success-box">
      &#10003; Registered successfully!
      &nbsp;<a href="signin.php">Sign In</a> &nbsp;|&nbsp; <a href="view.php">View Users</a>
    </div>
  <?php endif; ?>

  <form method="POST" action="register.php" enctype="multipart/form-data" novalidate>

    <!-- Profile Image -->
    <div class="row">
      <label>Profile Image</label>
      <div>
        <div class="img-upload-wrap">
          <div class="img-preview" id="imgPreview">
            <span class="placeholder">&#128100;</span>
          </div>
          <div>
            <label for="profile_image" class="img-upload-btn">&#128247; Choose Photo</label>
            <input type="file" id="profile_image" name="profile_image"
                   accept="image/jpeg,image/png,image/gif,image/webp"
                   onchange="previewImage(this)">
            <div style="font-size:.75rem;color:var(--muted);margin-top:4px;">JPEG, PNG, GIF, WebP · max 2 MB</div>
          </div>
        </div>
        <?=err('profile_image')?>
      </div>
    </div>

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
          <?php foreach (ALLOWED_SKILLS as $sk): ?>
          <label>
            <input type="checkbox" name="skills[]" value="<?=htmlspecialchars($sk)?>"
                   <?=hasSkill($sk)?'checked':''?>>
            <?=htmlspecialchars($sk)?>
          </label>
          <?php endforeach; ?>
        </div>
        <?=err('skills')?>
      </div>
    </div>

    <div class="row">
      <label for="uname">Username <span class="req">*</span></label>
      <div>
        <input type="text" id="uname" name="username" value="<?=v('username')?>"
               class="<?=isset($errors['username'])?'e':''?>"
               autocomplete="username">
        <?=err('username')?>
      </div>
    </div>

    <div class="row">
      <label for="pw">Password <span class="req">*</span></label>
      <div>
        <input type="password" id="pw" name="password"
               class="<?=isset($errors['password'])?'e':''?>"
               autocomplete="new-password">
        <?=err('password')?>
      </div>
    </div>

    <div class="row">
      <label for="confirm">Confirm Password <span class="req">*</span></label>
      <div>
        <input type="password" id="confirm" name="confirm"
               class="<?=isset($errors['confirm'])?'e':''?>"
               autocomplete="new-password">
        <?=err('confirm')?>
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
      <button type="reset"  class="btn btn-reset"
              onclick="document.getElementById('imgPreview').innerHTML='<span class=\'placeholder\'>&#128100;</span>'">Reset</button>
    </div>
  </form>

  <div class="footer-link">
    Already have an account? <a href="signin.php">Sign In</a>
  </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '<span class="placeholder">&#128100;</span>';
    }
}
</script>
</body>
</html>
