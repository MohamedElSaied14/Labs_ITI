<?php
require 'includes.php';

// Get the ID from the URL
$id = $_GET['id'] ?? '';
$r  = find_by_id($id);

if (!$r) {
    header('Location: data.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read updated values from the form
    $updated = [
        'id'         => $r['id'],          // keep the original ID
        'created_at' => $r['created_at'],  // keep the original timestamp
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
    if ($updated['first_name'] === '') $errors[] = 'First name is required.';
    if ($updated['last_name']  === '') $errors[] = 'Last name is required.';
    if ($updated['country']    === '') $errors[] = 'Please select a country.';
    if ($updated['gender']     === '') $errors[] = 'Please select a gender.';
    if ($updated['username']   === '') $errors[] = 'Username is required.';

    if (empty($errors)) {
        // Load all records, find the right one, replace it
        $records = read_all();
        foreach ($records as $i => $rec) {
            if ($rec['id'] === $id) {
                $records[$i] = $updated;
                break;
            }
        }
        write_all($records);
        header('Location: data.php?updated=1');
        exit;
    }

    // If errors, keep the attempted values to refill the form
    $r = $updated;
}

html_head('Edit Record');
?>

<div class="wrap">
  <div class="page-header">
    <h1>Edit Record</h1>
    <p>Updating: <?= $r['first_name'] ?> <?= $r['last_name'] ?></p>
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
            <input type="text" name="first_name" value="<?= $r['first_name'] ?>" required>
          </div>

          <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= $r['last_name'] ?>" required>
          </div>

          <div class="form-group full">
            <label>Address</label>
            <textarea name="address"><?= $r['address'] ?></textarea>
          </div>

          <div class="form-group">
            <label>Country</label>
            <div class="select-wrap">
              <select name="country" required>
                <option value="" disabled <?= $r['country']==='' ? 'selected':'' ?>>Select country</option>
                <?php foreach (['Egypt','Saudi Arabia','UAE','Jordan','USA','UK','Germany','France','Other'] as $c): ?>
                  <option value="<?= $c ?>" <?= $r['country']===$c ? 'selected':'' ?>><?= $c ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label>Gender</label>
            <div class="radio-row" style="padding-top:.45rem">
              <label class="r-item">
                <input type="radio" name="gender" value="Male" <?= $r['gender']==='Male' ? 'checked':'' ?>> Male
              </label>
              <label class="r-item">
                <input type="radio" name="gender" value="Female" <?= $r['gender']==='Female' ? 'checked':'' ?>> Female
              </label>
            </div>
          </div>

          <div class="form-group full">
            <label>Skills</label>
            <div class="check-row" style="padding-top:.2rem">
              <?php foreach (['PHP','J2SE','MySQL','PostgreSQL'] as $sk): ?>
                <label class="c-item">
                  <input type="checkbox" name="skills[]" value="<?= $sk ?>"
                    <?= in_array($sk, $r['skills']) ? 'checked':'' ?>> <?= $sk ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= $r['username'] ?>" required>
          </div>

          <div class="form-group">
            <label>Department</label>
            <input type="text" value="OpenSource" readonly style="opacity:.55;cursor:not-allowed">
          </div>

        </div>

        <div class="btn-row">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <a href="view.php?id=<?= urlencode($id) ?>" class="btn btn-ghost">Cancel</a>
        </div>

      </form>
    </div>
  </div>
</div>

<?php html_foot(); ?>
