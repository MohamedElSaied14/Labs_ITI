<?php
require 'includes.php';
session_start();

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read & clean inputs
    $old = [
        'first_name' => clean($_POST['first_name'] ?? ''),
        'last_name'  => clean($_POST['last_name']  ?? ''),
        'address'    => clean($_POST['address']    ?? ''),
        'country'    => clean($_POST['country']    ?? ''),
        'gender'     => clean($_POST['gender']     ?? ''),
        'skills'     => array_map('clean', $_POST['skills'] ?? []),
        'username'   => clean($_POST['username']   ?? ''),
        'department' => 'OpenSource',
    ];

    // Validate
    if ($old['first_name'] === '') $errors[] = 'First name is required.';
    if ($old['last_name']  === '') $errors[] = 'Last name is required.';
    if ($old['country']    === '') $errors[] = 'Please select a country.';
    if ($old['gender']     === '') $errors[] = 'Please select a gender.';
    if ($old['username']   === '') $errors[] = 'Username is required.';

    // If valid, store in session and go to review
    if (empty($errors)) {
        $_SESSION['form_data'] = $old;
        header('Location: done.php');
        exit;
    }
} else {
    $old = [
        'first_name'=>'','last_name'=>'','address'=>'','country'=>'',
        'gender'=>'','skills'=>[],'username'=>'','department'=>'OpenSource',
    ];
}

html_head('Registration');
?>

<div class="wrap">
  <div class="page-header">
    <h1>New Registration</h1>
    <p>Fill in the form — you will review before saving.</p>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-error">
      <?php foreach ($errors as $e): ?><?= $e ?><br><?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="">
        <div class="form-grid">

          <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?= $old['first_name'] ?>" required>
          </div>

          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= $old['last_name'] ?>" required>
          </div>

          <div class="form-group full">
            <label>Address</label>
            <textarea name="address"><?= $old['address'] ?></textarea>
          </div>

          <div class="form-group">
            <label>Country</label>
            <div class="select-wrap">
              <select name="country" required>
                <option value="" disabled <?= $old['country']==='' ? 'selected':'' ?>>Select country</option>
                <?php foreach (['Egypt','Saudi Arabia','UAE','Jordan','USA','UK','Germany','France','Other'] as $c): ?>
                  <option value="<?= $c ?>" <?= $old['country']===$c ? 'selected':'' ?>><?= $c ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Gender</label>
            <div class="radio-row" style="padding-top:.45rem">
              <label class="r-item">
                <input type="radio" name="gender" value="Male" <?= $old['gender']==='Male' ? 'checked':'' ?>> Male
              </label>
              <label class="r-item">
                <input type="radio" name="gender" value="Female" <?= $old['gender']==='Female' ? 'checked':'' ?>> Female
              </label>
            </div>
          </div>

          <div class="form-group full">
            <label>Skills</label>
            <div class="check-row" style="padding-top:.2rem">
              <?php foreach (['PHP','J2SE','MySQL','PostgreSQL'] as $sk): ?>
                <label class="c-item">
                  <input type="checkbox" name="skills[]" value="<?= $sk ?>"
                    <?= in_array($sk, $old['skills']) ? 'checked':'' ?>> <?= $sk ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= $old['username'] ?>" required>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password">
          </div>

          <div class="form-group full">
            <label>Department</label>
            <input type="text" value="OpenSource" readonly style="opacity:.55;cursor:not-allowed">
          </div>

        </div>
        <div class="btn-row">
          <button type="submit" class="btn btn-primary">Review &amp; Continue →</button>
          <button type="reset"  class="btn btn-ghost">Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php html_foot(); ?>
