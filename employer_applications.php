<?php
// views/dashboard/employer_applications.php
// Variables available from DashboardController::employerDashboard():
//   $jobs        – array of employer's jobs
//   $selectedJob – currently selected job_id (int, 0 = none)
//   $applications – array of application rows
//   $funnelData   – [['status'=>'...','count'=>N], …]
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Tracking — Employer Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0f1117;
            --surface:   #1a1d27;
            --border:    #2e3148;
            --accent:    #6c63ff;
            --accent2:   #ff6584;
            --text:      #e8eaf6;
            --muted:     #8b90b0;
            --success:   #4caf82;
            --warning:   #f0a500;
            --danger:    #e05252;
            --info:      #4ea8de;
        }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding: 2rem;
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .subtitle { color: var(--muted); font-size: 0.9rem; margin-bottom: 2rem; }

        /* ── Job selector ── */
        .job-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .job-selector label { color: var(--muted); font-size: 0.9rem; }
        .job-selector select {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 0.6rem 1rem;
            border-radius: 8px;
            font-size: 0.95rem;
            cursor: pointer;
            min-width: 280px;
        }
        .job-selector select:focus { outline: 2px solid var(--accent); }

        /* ── Stats row ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.2rem 1rem;
            text-align: center;
        }
        .stat-card .num { font-size: 2rem; font-weight: 700; }
        .stat-card .lbl { font-size: 0.75rem; color: var(--muted); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card.submitted .num { color: var(--info); }
        .stat-card.reviewed .num  { color: var(--warning); }
        .stat-card.shortlisted .num { color: var(--success); }
        .stat-card.rejected .num  { color: var(--danger); }

        /* ── Applications table ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            font-size: 1rem;
        }
        table { width: 100%; border-collapse: collapse; }
        th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            background: rgba(255,255,255,0.03);
        }
        td {
            padding: 0.9rem 1rem;
            border-top: 1px solid var(--border);
            font-size: 0.9rem;
            vertical-align: top;
        }
        tr:hover td { background: rgba(108,99,255,0.05); }

        .cover-preview {
            max-width: 260px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--muted);
            font-size: 0.85rem;
        }
        .resume-link {
            color: var(--accent);
            text-decoration: none;
            font-size: 0.85rem;
            padding: 4px 10px;
            border: 1px solid var(--accent);
            border-radius: 6px;
            white-space: nowrap;
        }
        .resume-link:hover { background: var(--accent); color: #fff; }

        /* ── Status dropdown ── */
        .status-select {
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 5px 8px;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .status-select:focus { outline: 2px solid var(--accent); }

        /* ── Status badges ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .badge-submitted   { background: rgba(78,168,222,0.15); color: var(--info); }
        .badge-reviewed    { background: rgba(240,165,0,0.15);  color: var(--warning); }
        .badge-shortlisted { background: rgba(76,175,130,0.15); color: var(--success); }
        .badge-rejected    { background: rgba(224,82,82,0.15);  color: var(--danger); }

        /* ── Toast ── */
        #toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            font-size: 0.9rem;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s;
            pointer-events: none;
            z-index: 9999;
        }
        #toast.show { opacity: 1; transform: translateY(0); }
        #toast.ok   { border-color: var(--success); color: var(--success); }
        #toast.err  { border-color: var(--danger);  color: var(--danger); }

        /* ── Chart ── */
        .chart-wrap {
            padding: 1.5rem;
            max-width: 600px;
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--muted);
        }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
    </style>
</head>
<body>

<h1>Application Tracking</h1>
<p class="subtitle">Review and manage applications for your job listings.</p>

<!-- ── Job selector ────────────────────────────────────────────── -->
<form method="GET" class="job-selector">
    <label for="job_id">Select a job:</label>
    <select name="job_id" id="job_id" onchange="this.form.submit()">
        <option value="">— Choose a job listing —</option>
        <?php foreach ($jobs as $job): ?>
            <option value="<?= $job['id'] ?>"
                <?= ($selectedJob === (int)$job['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($job['title']) ?>
                (<?= $job['application_count'] ?> apps)
                [<?= $job['status'] ?>]
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selectedJob > 0): ?>

<!-- ── Stats row ────────────────────────────────────────────────── -->
<?php
$funnelMap = array_column($funnelData, 'count', 'status');
$statuses  = ['Submitted','Reviewed','Shortlisted','Rejected'];
$classes   = ['submitted','reviewed','shortlisted','rejected'];
?>
<div class="stats-row">
    <?php foreach ($statuses as $i => $s): ?>
    <div class="stat-card <?= $classes[$i] ?>">
        <div class="num"><?= $funnelMap[$s] ?? 0 ?></div>
        <div class="lbl"><?= $s ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Applications table ────────────────────────────────────────── -->
<div class="card">
    <div class="card-header">Applications</div>
    <?php if (empty($applications)): ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            No applications received for this job yet.
        </div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Applicant</th>
                <th>Headline</th>
                <th>Applied On</th>
                <th>Cover Letter</th>
                <th>Resume</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applications as $app): ?>
            <tr id="row-<?= $app['id'] ?>">
                <td>
                    <strong><?= htmlspecialchars($app['seeker_name']) ?></strong><br>
                    <span style="color:var(--muted);font-size:0.8rem"><?= htmlspecialchars($app['seeker_email']) ?></span>
                </td>
                <td><?= htmlspecialchars($app['headline'] ?? '—') ?></td>
                <td><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                <td>
                    <span class="cover-preview" title="<?= htmlspecialchars($app['cover_letter']) ?>">
                        <?= htmlspecialchars($app['cover_letter']) ?>
                    </span>
                </td>
                <td>
                    <?php if ($app['resume_path']): ?>
                        <a class="resume-link"
                           href="/public/uploads/<?= htmlspecialchars(basename($app['resume_path'])) ?>"
                           target="_blank">⬇ Resume</a>
                    <?php else: ?>
                        <span style="color:var(--muted)">None</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                        $current = $app['status'];
                        $options = ['Reviewed','Shortlisted','Rejected'];
                    ?>
                    <select class="status-select"
                            data-app-id="<?= $app['id'] ?>"
                            data-current="<?= $current ?>"
                            onchange="updateStatus(this)">
                        <option value="Submitted" <?= $current==='Submitted' ?'selected':'' ?> disabled>Submitted</option>
                        <?php foreach ($options as $opt): ?>
                        <option value="<?= $opt ?>" <?= $current===$opt ?'selected':'' ?>><?= $opt ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="badge-<?= $app['id'] ?>" class="badge badge-<?= strtolower($current) ?>" style="margin-left:6px">
                        <?= $current ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<!-- ── Chart.js Funnel ────────────────────────────────────────────── -->
<div class="card">
    <div class="card-header">Application Funnel</div>
    <div class="chart-wrap">
        <canvas id="funnelChart" height="160"></canvas>
    </div>
</div>

<?php else: ?>
<div class="empty-state" style="margin-top:4rem">
    <div class="icon">📋</div>
    <p>Select a job from the dropdown above to view applications.</p>
</div>
<?php endif; ?>

<!-- ── Toast ────────────────────────────────────────────────────── -->
<div id="toast"></div>

<script>
// ── Status update AJAX ──────────────────────────────────────────
async function updateStatus(selectEl) {
    const appId  = selectEl.dataset.appId;
    const status = selectEl.value;

    try {
        const res = await fetch(`/api/applications/${appId}`, {
            method:  'PUT',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ status }),
        });
        const data = await res.json();

        if (data.success) {
            // Update badge colour + text in-place (no reload)
            const badge = document.getElementById(`badge-${appId}`);
            badge.className = `badge badge-${status.toLowerCase()}`;
            badge.textContent = status;
            selectEl.dataset.current = status;
            showToast('Status updated to ' + status, 'ok');
        } else {
            showToast(data.message || 'Update failed.', 'err');
            selectEl.value = selectEl.dataset.current; // revert
        }
    } catch (err) {
        showToast('Network error. Please try again.', 'err');
        selectEl.value = selectEl.dataset.current;
    }
}

// ── Toast helper ───────────────────────────────────────────────
function showToast(msg, type = 'ok') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = `show ${type}`;
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => { t.className = ''; }, 3000);
}

// ── Chart.js Funnel ────────────────────────────────────────────
<?php if ($selectedJob > 0 && !empty($funnelData)): ?>
(function () {
    const labels = <?= json_encode(array_column($funnelData, 'status')) ?>;
    const counts = <?= json_encode(array_column($funnelData, 'count')) ?>;
    const colors = {
        Submitted:   'rgba(78,168,222,0.8)',
        Reviewed:    'rgba(240,165,0,0.8)',
        Shortlisted: 'rgba(76,175,130,0.8)',
        Rejected:    'rgba(224,82,82,0.8)',
    };
    const bgColors = labels.map(l => colors[l] || '#888');

    new Chart(document.getElementById('funnelChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Applications',
                data:  counts,
                backgroundColor: bgColors,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
            },
            scales: {
                x: {
                    ticks: { color: '#8b90b0', stepSize: 1 },
                    grid:  { color: 'rgba(255,255,255,0.05)' },
                },
                y: {
                    ticks: { color: '#e8eaf6' },
                    grid:  { display: false },
                }
            }
        }
    });
})();
<?php endif; ?>
</script>

</body>
</html>
