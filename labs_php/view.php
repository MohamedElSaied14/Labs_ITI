<?php
require 'includes.php';

// Get the ID from the URL: view.php?id=rec_abc123
$id = $_GET['id'] ?? '';
$r  = find_by_id($id);

// If ID not found, go back to list
if (!$r) {
    header('Location: data.php');
    exit;
}

$prefix = ($r['gender'] === 'Female') ? 'Miss' : 'Mr';
html_head('View Record');
?>

<div class="wrap">
  <div class="page-header">
    <h1>View Record</h1>
    <p>Registered on <?= $r['created_at'] ?? '—' ?></p>
  </div>

  <div class="card">
    <div class="card-body">
      <p style="font-size:.85rem;font-family:var(--mono);color:var(--muted);margin-bottom:1.5rem">
        <?= $prefix ?>. <?= $r['first_name'] ?> <?= $r['last_name'] ?>
      </p>

      <table class="info-table">
        <tr>
          <td>Full name</td>
          <td><?= $r['first_name'] ?> <?= $r['last_name'] ?></td>
        </tr>
        <tr>
          <td>Address</td>
          <td><?= nl2br($r['address']) ?: '—' ?></td>
        </tr>
        <tr>
          <td>Country</td>
          <td><?= $r['country'] ?></td>
        </tr>
        <tr>
          <td>Gender</td>
          <td>
            <span class="badge <?= $r['gender']==='Female' ? 'badge-female':'badge-male' ?>">
              <?= $r['gender'] ?>
            </span>
          </td>
        </tr>
        <tr>
          <td>Skills</td>
          <td>
            <?php if (!empty($r['skills'])): ?>
              <?php foreach ($r['skills'] as $sk): ?>
                <span class="badge badge-skill"><?= $sk ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td>Username</td>
          <td style="font-family:var(--mono);font-size:.88rem"><?= $r['username'] ?></td>
        </tr>
        <tr>
          <td>Department</td>
          <td><?= $r['department'] ?></td>
        </tr>
        <tr>
          <td>Record ID</td>
          <td style="font-family:var(--mono);font-size:.8rem;color:var(--muted)"><?= $r['id'] ?></td>
        </tr>
        <tr>
          <td>Saved at</td>
          <td style="font-family:var(--mono);font-size:.85rem"><?= $r['created_at'] ?? '—' ?></td>
        </tr>
      </table>
    </div>

    <div class="card-body">
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <a href="edit.php?id=<?= urlencode($id) ?>" class="btn btn-ghost">Edit</a>
        <a href="delete.php?id=<?= urlencode($id) ?>" class="btn btn-danger"
           onclick="return confirm('Delete this record?')">Delete</a>
        <a href="data.php" class="btn btn-ghost">← Back to list</a>
      </div>
    </div>
  </div>
</div>

<?php html_foot(); ?>
