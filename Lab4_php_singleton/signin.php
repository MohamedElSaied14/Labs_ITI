<?php
// signin.php – Login page
require_once 'config.php';

// Already logged in → go straight to the list
if (isLoggedIn()) {
    header('Location: view.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = getDB()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            header('Location: view.php');
            exit;
        } else {
            $error = 'Invalid username or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Sign In</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --primary:#1a56db;--primary-h:#1e429f;--danger:#e02424;
  --border:#d1d5db;--bg:#f3f4f6;--card:#fff;--text:#111827;--muted:#6b7280;--r:6px;
}
body{font:14px/1.5 'Segoe UI',system-ui,sans-serif;background:var(--bg);color:var(--text);
     min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px 12px;}
.card{background:var(--card);border-radius:var(--r);box-shadow:0 2px 16px rgba(0,0,0,.1);
      width:100%;max-width:380px;padding:36px 36px 28px;}
h1{font-size:1.35rem;font-weight:700;text-align:center;color:var(--primary);margin-bottom:6px;}
.subtitle{text-align:center;color:var(--muted);font-size:.82rem;margin-bottom:28px;}
.field{margin-bottom:16px;}
.field label{display:block;font-weight:600;font-size:.85rem;margin-bottom:5px;}
.field input{width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--r);
             font:14px/1.5 inherit;background:#fafafa;color:var(--text);
             transition:border .15s,box-shadow .15s;}
.field input:focus{outline:none;border-color:var(--primary);
                   box-shadow:0 0 0 3px rgba(26,86,219,.15);background:#fff;}
.field input.e{border-color:var(--danger);}

.alert-error{background:#fde8e8;border:1px solid #f8b4b4;color:var(--danger);
             border-radius:var(--r);padding:10px 14px;font-size:.85rem;margin-bottom:18px;
             display:flex;align-items:center;gap:8px;}

.btn-login{width:100%;padding:10px;background:var(--primary);color:#fff;border:none;
           border-radius:var(--r);font:600 .95rem/1 inherit;cursor:pointer;
           margin-top:6px;transition:background .15s,transform .1s;}
.btn-login:hover{background:var(--primary-h);}
.btn-login:active{transform:scale(.98);}

.divider{text-align:center;color:var(--muted);font-size:.8rem;margin:20px 0 16px;
         position:relative;}
.divider::before,.divider::after{content:'';position:absolute;top:50%;
  width:42%;height:1px;background:var(--border);}
.divider::before{left:0;} .divider::after{right:0;}

.btn-register{display:block;width:100%;padding:9px;background:#f3f4f6;color:var(--text);
              border:1px solid var(--border);border-radius:var(--r);
              font:600 .9rem/1 inherit;cursor:pointer;text-align:center;
              text-decoration:none;transition:background .15s;}
.btn-register:hover{background:#e5e7eb;}
</style>
</head>
<body>
<div class="card">
  <h1>&#128274; Sign In</h1>
  <p class="subtitle">Enter your credentials to access the user list</p>

  <?php if ($error !== ''): ?>
    <div class="alert-error">&#10007; <?=htmlspecialchars($error)?></div>
  <?php endif; ?>

  <form method="POST" action="signin.php" novalidate>
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username"
             value="<?=htmlspecialchars($_POST['username'] ?? '')?>"
             class="<?=$error !== '' ? 'e' : ''?>"
             autocomplete="username" autofocus>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password"
             class="<?=$error !== '' ? 'e' : ''?>"
             autocomplete="current-password">
    </div>

    <button type="submit" class="btn-login">Sign In</button>
  </form>

  <div class="divider">or</div>
  <a href="register.php" class="btn-register">&#43; Create a New Account</a>
</div>
</body>
</html>
