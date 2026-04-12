<?php
require 'includes.php';
session_start();

// Guard: if no session data, go back
if (empty($_SESSION['form_data'])) {
    header('Location: registration.php');
    exit;
}

$d = $_SESSION['form_data'];

// When user clicks "Confirm & Save"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $records   = read_all();          // load existing records from file
    $new_record = $d;
    $new_record['id']         = uniqid('rec_', true);  // unique ID
    $new_record['created_at'] = date('Y-m-d H:i:s');   // timestamp
    $records[] = $new_record;         // add to array
    write_all($records);              // save back to file
    unset($_SESSION['form_data']);    // clear session
    header('Location: data.php?saved=1');
    exit;
}

$prefix = ($d['gender'] === 'Female') ? 'Miss' : 'Mr';
html_head('Review');
?>

<div class="wrap">
  <div class="page-header">
    <h1>Review Your Information</h1>
    <p>Please check everything before saving.</p>
  </div>

  <div class="card">
    <div class="card-body">
      <p style="font-size:.82rem;color:var(--muted);margin-bottom:1.25rem;font-family:var(--mono)">
        Thanks, <?= $prefix ?>. <?= $d['first_name'] ?> <?= $d['last_name'] ?> — please confirm the details below.
      </p>

      <table class="info-table">
        <tr>
          <td>Name</td>
          <td><?= $d['first_name'] ?> <?= $d['last_name'] ?></td>
        </tr>
        <tr>
          <td>Address</td>
          <td><?= nl2br($d['address']) ?: '—' ?></td>
        </tr>
        <tr>
          <td>Country</td>
          <td><?= $d['country'] ?: '—' ?></td>
        </tr>
        <tr>
          <td>Gender</td>
          <td>
            <span class="badge <?= $d['gender']==='Female' ? 'badge-female':'badge-male' ?>">
              <?= $d['gender'] ?>
            </span>
          </td>
        </tr>
        <tr>
          <td>Skills</td>
          <td>
            <?php if (!empty($d['skills'])): ?>
              <?php foreach ($d['skills'] as $sk): ?>
                <span class="badge badge-skill"><?= $sk ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Username</td>
          <td><?= $d['username'] ?: '—' ?></td>
        </tr>
        <tr>
          <td>Department</td>
          <td><?= $d['department'] ?></td>
        </tr>
      </table>
    </div>

    <div class="card-body">
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">

        <!-- Confirm saves the data -->
        <form method="POST" action="">
          <input type="hidden" name="confirm" value="1">
          <button type="submit" class="btn btn-green">Confirm &amp; Save</button>
        </form>

        <!-- Go back to edit -->
        <a href="registration.php" class="btn btn-ghost">← Edit</a>

      </div>
    </div>
  </div>
</div>

<?php html_foot(); ?>
