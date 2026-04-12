<?php
require 'includes.php';

$records = read_all();
$saved   = isset($_GET['saved']);
$deleted = isset($_GET['deleted']);
$updated = isset($_GET['updated']);

html_head('All Records');
?>

<div class="wrap wrap-wide">
  <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem">
    <div>
      <h1>All Records</h1>
      <p><?= count($records) ?> registration<?= count($records) !== 1 ? 's':'' ?> stored in data.json</p>
    </div>
    <a href="registration.php" class="btn btn-primary">+ New Registration</a>
  </div>

  <?php if ($saved):   ?><div class="alert alert-success">Record saved successfully.</div><?php endif; ?>
  <?php if ($deleted): ?><div class="alert alert-error">Record deleted.</div><?php endif; ?>
  <?php if ($updated): ?><div class="alert alert-success">Record updated successfully.</div><?php endif; ?>

  <div class="card">
    <?php if (empty($records)): ?>
      <div class="empty">
        <strong>No records yet</strong>
        <p>Submit the registration form to add your first entry.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Username</th>
              <th>Country</th>
              <th>Gender</th>
              <th>Skills</th>
              <th>Saved at</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($records as $i => $r): ?>
            <tr>
              <td style="color:var(--muted);font-family:var(--mono);font-size:.8rem"><?= $i + 1 ?></td>
              <td><strong><?= $r['first_name'] ?> <?= $r['last_name'] ?></strong></td>
              <td style="font-family:var(--mono);font-size:.82rem"><?= $r['username'] ?></td>
              <td><?= $r['country'] ?></td>
              <td>
                <span class="badge <?= $r['gender']==='Female' ? 'badge-female':'badge-male' ?>">
                  <?= $r['gender'] ?>
                </span>
              </td>
              <td>
                <?php foreach ($r['skills'] as $sk): ?>
                  <span class="badge badge-skill"><?= $sk ?></span>
                <?php endforeach; ?>
                <?php if (empty($r['skills'])): ?>—<?php endif; ?>
              </td>
              <td style="font-size:.8rem;color:var(--muted);font-family:var(--mono);white-space:nowrap">
                <?= $r['created_at'] ?? '—' ?>
              </td>
              <td>
                <div class="td-actions">
                  <a href="view.php?id=<?= urlencode($r['id']) ?>"   class="btn btn-sm btn-blue">View</a>
                  <a href="edit.php?id=<?= urlencode($r['id']) ?>"   class="btn btn-sm btn-ghost">Edit</a>
                  <a href="delete.php?id=<?= urlencode($r['id']) ?>" class="btn btn-sm btn-danger"
                     onclick="return confirm('Delete this record?')">Delete</a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php html_foot(); ?>
