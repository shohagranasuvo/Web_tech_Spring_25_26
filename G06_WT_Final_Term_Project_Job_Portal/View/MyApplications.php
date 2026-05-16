<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Applications</title>

<style>
body {
    font-family: Arial;
    background: #f4f7f6;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 1000px;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

h2 {
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
    color: #333;
}

/* Table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background: #f8f9fa;
}

/* Status badges */
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    color: white;
}

.submitted { background: #0dcaf0; }
.reviewed { background: #ffc107; color: black; }
.shortlisted { background: #198754; }
.rejected { background: #dc3545; }

a {
    text-decoration: none;
    color: #007bff;
}
</style>

</head>

<body>

<div class="container">
    <h2>⭐ My Applications</h2>

    <?php if ($applications && $applications->num_rows > 0): ?>

    <table>
        <tr>
            <th>Job Title</th>
            <th>Company</th>
            <th>Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $applications->fetch_assoc()): ?>

        <?php
            $status = strtolower($row['status']);
            $date = date("M d, Y", strtotime($row['created_at']));
        ?>

        <tr>
            <td><b><?php echo htmlspecialchars($row['job_title']); ?></b></td>
            <td><?php echo htmlspecialchars($row['company_name']); ?></td>
            <td><?php echo $date; ?></td>

            <td>
                <span class="badge <?php echo $status; ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                </span>
            </td>

            <td>
                <a href="../View/JobDetail.php?id=<?php echo $row['job_id']; ?>">
                    View Job
                </a>
            </td>
        </tr>

        <?php endwhile; ?>

    </table>

    <?php else: ?>
        <p style="color: gray;">No applications found.</p>
    <?php endif; ?>

</div>

</body>
</html>