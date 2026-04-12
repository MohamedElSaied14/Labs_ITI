<?php
// ─── DATA FILE PATH ───────────────────────────────────────────────────────────
define('DATA_FILE', __DIR__ . '/data.json');

// ─── READ ALL RECORDS FROM FILE ───────────────────────────────────────────────
function read_all() {
    if (!file_exists(DATA_FILE)) return [];
    $json = file_get_contents(DATA_FILE);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

// ─── WRITE ALL RECORDS TO FILE ────────────────────────────────────────────────
function write_all($records) {
    file_put_contents(DATA_FILE, json_encode($records, JSON_PRETTY_PRINT));
}

// ─── FIND ONE RECORD BY ID ────────────────────────────────────────────────────
function find_by_id($id) {
    foreach (read_all() as $row) {
        if ($row['id'] === $id) return $row;
    }
    return null;
}

// ─── CLEAN USER INPUT ─────────────────────────────────────────────────────────
function clean($val) {
    return htmlspecialchars(trim($val ?? ''));
}

// ─── SHARED HTML HEAD ─────────────────────────────────────────────────────────
function html_head($title) { ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?> — OpenSource Dept</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg: #f7f5f0;
  --surface: #ffffff;
  --ink: #18181b;
  --muted: #71717a;
  --faint: #e4e4e7;
  --accent: #dc4a2d;
  --accent-soft: #fdf1ee;
  --green: #16a34a;
  --green-soft: #f0fdf4;
  --blue: #2563eb;
  --blue-soft: #eff6ff;
  --yellow-soft: #fefce8;
  --yellow: #ca8a04;
  --mono: 'JetBrains Mono', monospace;
  --sans: 'Sora', sans-serif;
  --radius: 8px;
  --radius-lg: 12px;
  --shadow: 0 1px 4px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.04);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: var(--sans);
  background: var(--bg);
  color: var(--ink);
  min-height: 100vh;
  font-size: 15px;
  line-height: 1.6;
}
a { color: inherit; text-decoration: none; }

/* ── NAV ── */
.topbar {
  background: var(--ink);
  padding: 0 2rem;
  display: flex;
  align-items: center;
  gap: 2rem;
  height: 52px;
}
.topbar-brand {
  font-family: var(--mono);
  font-size: 12px;
  color: #fff;
  letter-spacing: .12em;
  font-weight: 500;
  white-space: nowrap;
}
.topbar-brand span { color: var(--accent); }
.topbar-nav { display: flex; gap: 4px; }
.topbar-nav a {
  font-size: 12px;
  color: #a1a1aa;
  padding: 5px 12px;
  border-radius: var(--radius);
  transition: all .15s;
  font-weight: 400;
}
.topbar-nav a:hover, .topbar-nav a.active {
  background: rgba(255,255,255,.1);
  color: #fff;
}

/* ── PAGE WRAPPER ── */
.wrap {
  max-width: 820px;
  margin: 0 auto;
  padding: 2.5rem 1.5rem 4rem;
}
.wrap-wide { max-width: 1000px; }

/* ── PAGE HEADER ── */
.page-header { margin-bottom: 2rem; }
.page-header h1 {
  font-size: 1.5rem;
  font-weight: 600;
  letter-spacing: -.02em;
  margin-bottom: .25rem;
}
.page-header p { font-size: .875rem; color: var(--muted); }

/* ── CARD ── */
.card {
  background: var(--surface);
  border: 1px solid var(--faint);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow);
}
.card-body { padding: 2rem 2.2rem; }
.card-body + .card-body { border-top: 1px solid var(--faint); }

/* ── FORM ── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; }
.form-group { display: flex; flex-direction: column; gap: .35rem; }
.form-group.full { grid-column: 1 / -1; }
label {
  font-size: .72rem;
  font-weight: 600;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--muted);
}
input[type=text], input[type=password], textarea, select {
  font-family: var(--sans);
  font-size: .9rem;
  color: var(--ink);
  background: var(--bg);
  border: 1px solid var(--faint);
  border-radius: var(--radius);
  padding: .55rem .85rem;
  outline: none;
  transition: border-color .15s, background .15s;
  width: 100%;
  appearance: none;
  -webkit-appearance: none;
}
input:focus, textarea:focus, select:focus {
  border-color: var(--ink);
  background: #fff;
}
textarea { resize: vertical; min-height: 80px; }
.select-wrap { position: relative; }
.select-wrap::after {
  content: '▾';
  position: absolute; right: .85rem; top: 50%;
  transform: translateY(-50%);
  pointer-events: none; font-size: .7rem; color: var(--muted);
}

/* ── RADIO / CHECKBOX ── */
.radio-row, .check-row { display: flex; gap: 1.5rem; flex-wrap: wrap; padding-top: .1rem; }
.r-item, .c-item {
  display: flex; align-items: center; gap: .4rem;
  font-size: .88rem; cursor: pointer; color: var(--ink);
}
.r-item input, .c-item input { width: auto; accent-color: var(--accent); cursor: pointer; }

/* ── BUTTONS ── */
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
  font-family: var(--mono); font-size: .72rem; font-weight: 500;
  letter-spacing: .08em; text-transform: uppercase;
  padding: .55rem 1.25rem; border-radius: var(--radius);
  border: 1px solid transparent; cursor: pointer;
  transition: all .15s; white-space: nowrap;
}
.btn-primary { background: var(--ink); color: #fff; }
.btn-primary:hover { background: #27272a; }
.btn-danger  { background: var(--accent); color: #fff; }
.btn-danger:hover { background: #b83b23; }
.btn-ghost   { background: transparent; color: var(--muted); border-color: var(--faint); }
.btn-ghost:hover { border-color: var(--ink); color: var(--ink); }
.btn-green   { background: var(--green); color: #fff; }
.btn-green:hover { background: #15803d; }
.btn-blue    { background: var(--blue); color: #fff; }
.btn-blue:hover { background: #1d4ed8; }
.btn-sm { padding: .35rem .85rem; font-size: .65rem; }
.btn-row { display: flex; gap: .75rem; flex-wrap: wrap; margin-top: 1.75rem; }

/* ── ALERT ── */
.alert {
  padding: .75rem 1rem; border-radius: var(--radius);
  font-size: .85rem; margin-bottom: 1.5rem;
  border-left: 3px solid;
  border-top-left-radius: 0; border-bottom-left-radius: 0;
}
.alert-error   { background: var(--accent-soft); color: #9f2d1a; border-color: var(--accent); }
.alert-success { background: var(--green-soft);  color: #15803d; border-color: var(--green); }

/* ── TABLE ── */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: .875rem; }
th {
  font-family: var(--mono); font-size: .65rem; font-weight: 500;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--muted); text-align: left;
  padding: .75rem 1rem; border-bottom: 1px solid var(--faint);
  background: var(--bg); white-space: nowrap;
}
td {
  padding: .8rem 1rem; border-bottom: 1px solid var(--faint);
  vertical-align: middle; color: var(--ink);
}
tr:last-child td { border-bottom: none; }
tr:hover td { background: var(--bg); }
.td-actions { display: flex; gap: .4rem; }

/* ── BADGES ── */
.badge {
  display: inline-block; font-family: var(--mono);
  font-size: .65rem; font-weight: 500;
  padding: .2rem .55rem; border-radius: 4px;
  margin: .1rem .1rem 0 0;
}
.badge-skill { background: var(--ink); color: #fff; }
.badge-male   { background: var(--blue-soft); color: #1d4ed8; }
.badge-female { background: #fdf4ff; color: #7e22ce; }

/* ── VIEW CARD ── */
.info-table { width: 100%; border-collapse: collapse; }
.info-table tr + tr td { border-top: 1px solid var(--faint); }
.info-table td { padding: .75rem 0; vertical-align: top; }
.info-table td:first-child {
  font-family: var(--mono); font-size: .72rem; font-weight: 500;
  text-transform: uppercase; letter-spacing: .08em;
  color: var(--muted); width: 130px; padding-right: 1rem;
}

/* ── EMPTY STATE ── */
.empty {
  text-align: center; padding: 4rem 2rem; color: var(--muted);
}
.empty p { font-size: .9rem; margin-top: .5rem; }

/* ── RESPONSIVE ── */
@media(max-width: 600px) {
  .form-grid { grid-template-columns: 1fr; }
  .topbar { padding: 0 1rem; gap: 1rem; }
  .wrap { padding: 1.5rem 1rem 3rem; }
  .card-body { padding: 1.5rem 1.2rem; }
}
</style>
</head>
<body>
<nav class="topbar">
  <div class="topbar-brand">OPEN<span>SOURCE</span></div>
  <div class="topbar-nav">
    <a href="registration.php">Register</a>
    <a href="data.php">All Records</a>
  </div>
</nav>
<?php } // end html_head

// ─── SHARED HTML FOOT ─────────────────────────────────────────────────────────
function html_foot() { ?>
</body>
</html>
<?php }
