<?php
// views/dashboard/admin_panel.php
// Variables from DashboardController::adminPanel():
//   $jobs        – all jobs (with optional filter applied)
//   $categories  – all categories for filter dropdown
//   $summary     – ['total_jobs', 'total_applications', 'by_category']
//   $catFilter   – currently selected category_id (int|null)
//   $statusFilter – currently selected status string|null
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — Job Portal</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #f5f7ff;
            --surface: #ffffff;
            --border:  #dde1f5;
            --accent:  #4f46e5;
            --danger:  #dc2626;
            --success: #16a34a;
            --text:    #1e1b4b;
            --muted:   #6b7280;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 2rem;
        }

        h1 { font-size: 1.7rem; font-weight: 800; margin-bottom: 0.25rem; }
        .subtitle { color: var(--muted); font-size: 0.9rem; margin-bottom: 2rem; }

        /* ── Summary cards ── */
        .summary-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .sum-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.2rem;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .sum-card .big { font-size: 2.2rem; font-weight: 700; color: var(--accent); }
        .sum-card .lbl { font-size: 0.78rem; color: var(--muted); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.05em; }

        /* ── Filters ── */
        .filter-bar {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        .filter-bar label { font-size: 0.82rem; color: var(--muted); display: block; margin-bottom: 4px; }
        .filter-bar select, .filter-bar input {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.55rem 0.9rem;
            font-size: 0.9rem;
            color: var(--text);
        }
        .btn {
            padding: 0.55rem 1.2rem;
            border-radius: 8px;
            border: none;
            font-size: 0.9rem;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-ghost   { background: transparent; border: 1px solid var(--border); color: var(--muted); }
        .btn-danger  { background: var(--danger); color: #fff; }

        /* ── Jobs table ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-weight: 700;
            font-size: 1rem;
            background: rgba(79,70,229,0.04);
        }
        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        th {
            padding: 0.7rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            background: rgba(0,0,0,0.02);
        }
        td {
            padding: 0.85rem 1rem;
            border-top: 1px solid var(--border);
            vertical-align: middle;
        }
        tr:hover td { background: rgba(79,70,229,0.03); }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .badge-active  { background: #dcfce7; color: #16a34a; }
        .badge-closed  { background: #fee2e2; color: #dc2626; }

        .close-btn {
            background: transparent;
            border: 1px solid var(--danger);
            color: var(--danger);
            padding: 4px 12px;
            border-radius: 7px;
            font-size: 0.8rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }
        .close-btn:hover { background: var(--danger); color: #fff; }
        .close-btn:disabled { opacity: 0.4; cursor: default; }

        /* ── By-category table ── */
        .cat-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .cat-table th, .cat-table td { padding: 0.7rem 1.5rem; text-align: left; border-top: 1px solid var(--border); }
        .cat-table th { color: var(--muted); font-size: 0.78rem; text-transform: uppercase; }
        .cat-bar {
            display: inline-block;
            height: 8px;
            border-radius: 4px;
            background: var(--accent);
            vertical-align: middle;
            margin-right: 8px;
        }

        /* ── Toast ── */
        #toast {
            position: fixed; bottom: 2rem; right: 2rem;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 10px; padding: 0.75rem 1.25rem;
            font-size: 0.9rem; opacity: 0; transform: translateY(10px);
            transition: all 0.3s; pointer-events: none; z-index: 9999;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        #toast.show { opacity: 1; transform: translateY(0); }
        #toast.ok  { border-color: var(--success); color: var(--success); }
        #toast.err { border-color: var(--danger);  color: var(--danger); }
    </style>
</head>
<body>

<h1>⚙️ Admin Panel</h1>
<p class="subtitle">Manage all job listings across all employers.</p>

<!-- ── Summary ── -->
<div class="summary-row">
    <div class="sum-card">
        <div class="big"><?= $summary['total_jobs'] ?></div>
        <div class="lbl">Total Jobs</div>
    </div>
    <div class="sum-card">
        <div class="big"><?= $summary['total_applications'] ?></div>
        <div class="lbl">Total Applications</div>
    </div>
    <div class="sum-card">
        <div class="big"><?= count($categories) ?></div>
        <div class="lbl">Categories</div>
    </div>
</div>

<!-- ── Filters ── -->
<form method="GET" class="filter-bar">
    <div>
        <label>Category</label>
        <select name="category_id">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"
                <?= ($catFilter === (int)$cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>Status</label>
        <select name="status">
            <option value="">All Statuses</option>
            <option value="active"  <?= $statusFilter==='active'  ?'selected':'' ?>>Active</option>
            <option value="closed"  <?= $statusFilter==='closed'  ?'selected':'' ?>>Closed</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="?" class="btn btn-ghost">Reset</a>
</form>

<!-- ── All Jobs Table ── -->
<div class="card">
    <div class="card-header">All Job Listings (<?= count($jobs) ?>)</div>
    <?php if (empty($jobs)): ?>
        <p style="padding:2rem; color:var(--muted); text-align:center">No jobs found.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Employer / Company</th>
                <th>Category</th>
                <th>Deadline</th>
                <th>Apps</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job): ?>
            <tr id="job-row-<?= $job['id'] ?>">
                <td style="color:var(--muted)"><?= $job['id'] ?></td>
                <td><strong><?= htmlspecialchars($job['title']) ?></strong></td>
                <td>
                    <?= htmlspecialchars($job['employer_name']) ?><br>
                    <span style="color:var(--muted);font-size:0.8rem"><?= htmlspecialchars($job['company_name'] ?? '') ?></span>
                </td>
                <td><?= htmlspecialchars($job['category'] ?? '—') ?></td>
                <td><?= $job['deadline'] ? date('M d, Y', strtotime($job['deadline'])) : '—' ?></td>
                <td><?= $job['application_count'] ?></td>
                <td>
                    <span id="status-badge-<?= $job['id'] ?>"
                          class="badge badge-<?= $job['status'] ?>">
                        <?= ucfirst($job['status']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($job['status'] === 'active'): ?>
                    <button class="close-btn"
                            id="close-btn-<?= $job['id'] ?>"
                            onclick="closeJob(<?= $job['id'] ?>, this)">
                        Close Job
                    </button>
                    <?php else: ?>
                    <span style="color:var(--muted); font-size:0.8rem">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- ── Applications by Category ── -->
<div class="card">
    <div class="card-header">Applications by Category</div>
    <?php
    $maxCat = max(array_column($summary['by_category'], 'total_applications') ?: [1]);
    ?>
    <table class="cat-table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Applications</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary['by_category'] as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>
                    <span class="cat-bar"
                          style="width:<?= $maxCat > 0 ? round($row['total_applications']/$maxCat*120) : 0 ?>px">
                    </span>
                    <?= $row['total_applications'] ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="toast"></div>

<script>
async function closeJob(jobId, btn) {
    if (!confirm('Close this job? Seekers will no longer see it on the board.')) return;

    btn.disabled    = true;
    btn.textContent = 'Closing…';

    try {
        const res  = await fetch(`/api/admin/jobs/${jobId}`, { method: 'DELETE' });
        const data = await res.json();

        if (data.success) {
            // Update badge without page reload
            const badge = document.getElementById(`status-badge-${jobId}`);
            badge.className   = 'badge badge-closed';
            badge.textContent = 'Closed';
            btn.remove();
            showToast('Job closed successfully.', 'ok');
        } else {
            btn.disabled    = false;
            btn.textContent = 'Close Job';
            showToast(data.message || 'Failed to close job.', 'err');
        }
    } catch (e) {
        btn.disabled    = false;
        btn.textContent = 'Close Job';
        showToast('Network error. Please try again.', 'err');
    }
}

function showToast(msg, type = 'ok') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = `show ${type}`;
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => { t.className = ''; }, 3000);
}
</script>
</body>
</html>
